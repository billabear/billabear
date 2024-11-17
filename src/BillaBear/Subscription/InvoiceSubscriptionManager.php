<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Credit\CreditAdjustmentRecorder;
use BillaBear\Entity\Customer;
use BillaBear\Exception\Invoice\NothingToInvoiceException;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Security\ApiUser;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceSubscriptionManager implements SubscriptionManagerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionFactory $subscriptionFactory,
        private PaymentCardRepositoryInterface $paymentDetailsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private EventDispatcherInterface $dispatcher,
        private InvoiceGenerator $invoiceGenerator,
        private PlanManagerInterface $planManager,
        private InvoiceCharger $invoiceCharger,
        private Security $security,
        private CreditAdjustmentRecorder $creditAdjustmentRecorder,
        private TrialManager $trialManager,
    ) {
    }

    public function startSubscription(CustomerInterface $customer, SubscriptionPlan|Plan $plan, Price|PlanPrice $planPrice, ?PaymentCard $paymentDetails = null, int $seatNumbers = 1, ?bool $hasTrial = null, ?int $trialLengthDays = null): Subscription
    {
        $subscription = $this->subscriptionFactory->create($customer, $plan, $planPrice, $paymentDetails, $seatNumbers, $hasTrial, $trialLengthDays);

        if ($subscription->isHasTrial()) {
            $this->trialManager->startTrialProcess($subscription);
        }

        try {
            $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, [$subscription]);

            if (Customer::BILLING_TYPE_CARD === $customer->getBillingType() && SubscriptionStatus::ACTIVE === $subscription->getStatus()) {
                $this->invoiceCharger->chargeInvoice($invoice);
            }
        } catch (NothingToInvoiceException) {
        }

        $this->dispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);

        return $subscription;
    }

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        if (!$startSubscriptionDto->getPaymentDetailsId()) {
            $paymentDetails = $this->paymentDetailsRepository->getDefaultPaymentCardForCustomer($customer);
        } else {
            $paymentDetails = $this->paymentDetailsRepository->findById($startSubscriptionDto->getPaymentDetailsId());
        }

        $plan = $this->planManager->getPlanByName($startSubscriptionDto->getPlanName());
        $planPrice = $plan->getPriceForPaymentSchedule($startSubscriptionDto->getSchedule(), $startSubscriptionDto->getCurrency());

        return $this->startSubscription($customer, $plan, $planPrice, $paymentDetails, $startSubscriptionDto->getSeatNumbers());
    }

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription
    {
        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->endAtEndOfPeriod();

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        $subscription->setStatus(SubscriptionStatus::CANCELLED);
        $subscription->setActive(false);
        $subscription->endNow();

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->setEndedAt($dateTime);
        $subscription->setValidUntil($dateTime);

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    /**
     * @var \BillaBear\Entity\Price
     */
    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        /** @var \BillaBear\Entity\Price $oldPrice */
        $oldPrice = $subscription->getPrice();

        $diff = $price->getAsMoney()->minus($oldPrice->getAsMoney());
        $customer = $subscription->getCustomer();
        if (BillingChangeTiming::INSTANTLY === $billingChangeTiming) {
            $diff = $this->calculateProrateDiff($subscription, $oldPrice, $price);

            if ($diff->isPositive()) {
                $invoice = $this->invoiceGenerator->generateForCustomerAndUpgrade(
                    $customer,
                    $subscription->getSubscriptionPlan(),
                    $subscription->getSubscriptionPlan(),
                    $oldPrice,
                    $price,
                    $diff,
                );

                if (Customer::BILLING_TYPE_CARD === $customer->getBillingType()) {
                    try {
                        $this->invoiceCharger->chargeInvoice($invoice);
                    } catch (PaymentFailureException $e) {
                        $this->getLogger()->warning(
                            'Failed to make payment charge when changing subscription price',
                            ['reason' => $e->getReason()->value]
                        );
                    }
                }

                $this->dispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);
            } else {
                $user = $this->security->getUser();
                if ($user instanceof ApiUser) {
                    $user = null;
                }
                $this->creditAdjustmentRecorder->createRecord('credit', $customer, $diff->abs(), 'price change', $user);
            }

            if (!$oldPrice->isSameSchedule($price)) {
                $dateStr = sprintf('+1 %s', $price->getSchedule());
                $newValid = new \DateTime($dateStr);
                $subscription->setValidUntil($newValid);
                $subscription->setStartOfCurrentPeriod(new \DateTime());
            }
        }

        $subscription->setPrice($price);
        $subscription->setMoneyAmount($price->getAsMoney());
        $subscription->setPaymentSchedule($price->getSchedule());
    }

    public function changeSubscriptionPlan(Subscription $subscription, SubscriptionPlan|Plan $plan, Price|PlanPrice $price, BillingChangeTiming $billingChangeTiming): void
    {
        /** @var \BillaBear\Entity\Price $oldPrice */
        $oldPrice = $subscription->getPrice();
        $oldPlan = $subscription->getSubscriptionPlan();
        $customer = $subscription->getCustomer();
        if (BillingChangeTiming::INSTANTLY === $billingChangeTiming) {
            $diff = $this->calculateProrateDiff($subscription, $oldPrice, $price);

            if ($diff->isPositive()) {
                $invoice = $this->invoiceGenerator->generateForCustomerAndUpgrade($customer, $oldPlan, $plan, $oldPrice, $price, $diff);

                if (Customer::BILLING_TYPE_CARD === $customer->getBillingType()) {
                    try {
                        $this->invoiceCharger->chargeInvoice($invoice);
                    } catch (PaymentFailureException $e) {
                        $this->getLogger()->warning(
                            'Failed to make payment charge when changing subscription plan',
                            ['reason' => $e->getReason()->value]
                        );
                    }
                }

                $this->dispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);
            } else {
                $user = $this->security->getUser();
                if ($user instanceof ApiUser) {
                    $user = null;
                }
                $this->creditAdjustmentRecorder->createRecord('credit', $customer, $diff->abs(), 'plan change', $user);
            }
            if (!$oldPrice->isSameSchedule($price)) {
                $dateStr = sprintf('+1 %s', $price->getSchedule());
                $newValid = new \DateTime($dateStr);
                $subscription->setValidUntil($newValid);
                $subscription->setStartOfCurrentPeriod(new \DateTime());
            }
        }

        $subscription->setPrice($price);
        $subscription->setMoneyAmount($price->getAsMoney());
        $subscription->setSubscriptionPlan($plan);
        $subscription->setPlanName($plan->getName());
    }

    /**
     * @throws MoneyMismatchException
     */
    public function calculateProrateDiff(Subscription $subscription, \BillaBear\Entity\Price $oldPrice, Price|\BillaBear\Entity\Price $price): Money
    {
        $now = new \DateTime();
        $originalInterval = $subscription->getStartOfCurrentPeriod()->diff($subscription->getValidUntil());
        $timeLeftInterval = $subscription->getStartOfCurrentPeriod()->diff($now);
        if (0 !== $originalInterval->days) {
            $percentage = ($timeLeftInterval->days / $originalInterval->days);
        } else {
            $percentage = 0;
        }

        $oldPriceMoney = $oldPrice->getAsMoney();
        $proRateAmount = $oldPriceMoney->minus($oldPriceMoney->multipliedBy($percentage, RoundingMode::DOWN));

        return $price->getAsMoney()->minus($proRateAmount);
    }
}
