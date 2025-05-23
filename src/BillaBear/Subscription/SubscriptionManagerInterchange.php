<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\Customer;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Subscription\SubscriptionManager;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

#[Lazy]
class SubscriptionManagerInterchange implements SubscriptionManagerInterface
{
    private bool $stripeBillingEnabled = false;

    public function __construct(
        private SubscriptionManager $stripeBillingManager,
        private InvoiceSubscriptionManager $invoiceSubscriptionManager,
        private SettingsRepositoryInterface $settingsRepository,
        private CustomerSubscriptionEventCreator $customerSubscriptionEventCreator,
    ) {
        $this->stripeBillingEnabled = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling();
    }

    /**
     * @param Customer $customer
     */
    public function startSubscription(CustomerInterface $customer, Plan|SubscriptionPlan $plan, PlanPrice|Price $planPrice, ?PaymentCard $paymentDetails = null, int $seatNumbers = 1, ?bool $hasTrial = null, ?int $trialLengthDays = null): Subscription
    {
        if (!$this->stripeBillingEnabled || Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->startSubscription($customer, $plan, $planPrice, $paymentDetails, $seatNumbers, $hasTrial, $trialLengthDays);
        }

        return $this->stripeBillingManager->startSubscription($customer, $plan, $planPrice, $paymentDetails, $seatNumbers, $hasTrial, $trialLengthDays);
    }

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        if (!$this->stripeBillingEnabled || Customer::BILLING_TYPE_INVOICE === $customer->getBillingType()) {
            return $this->invoiceSubscriptionManager->startSubscriptionWithDto($customer, $startSubscriptionDto);
        }

        return $this->stripeBillingManager->startSubscriptionWithDto($customer, $startSubscriptionDto);
    }

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription
    {
        $customer = $subscription->getCustomer();
        if (!$this->stripeBillingEnabled || Customer::BILLING_TYPE_INVOICE === $customer->getBillingType() || null === $subscription->getMainExternalReference()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
        }

        return $this->stripeBillingManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType() || null === $subscription->getMainExternalReference()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionInstantly($subscription);
        }

        return $this->stripeBillingManager->cancelSubscriptionInstantly($subscription);
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        $customer = $subscription->getCustomer();
        if (Customer::BILLING_TYPE_INVOICE === $customer->getBillingType() || null === $subscription->getMainExternalReference()) {
            return $this->invoiceSubscriptionManager->cancelSubscriptionOnDate($subscription, $dateTime);
        }

        return $this->stripeBillingManager->cancelSubscriptionOnDate($subscription, $dateTime);
    }

    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        if (!$this->stripeBillingEnabled || Customer::BILLING_TYPE_INVOICE === $subscription->getCustomer()->getBillingType() || null === $subscription->getMainExternalReference()) {
            $this->invoiceSubscriptionManager->changeSubscriptionPrice($subscription, $price, $billingChangeTiming);

            return;
        }

        $this->stripeBillingManager->changeSubscriptionPrice($subscription, $price, $billingChangeTiming);
    }

    public function changeSubscriptionPlan(Subscription $subscription, Plan|SubscriptionPlan $plan, PlanPrice|Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        if ($subscription->getPrice()->getAsMoney()->isLessThan($price->getAsMoney())) {
            $eventType = CustomerSubscriptionEventType::UPGRADED;
        } else {
            $eventType = CustomerSubscriptionEventType::DOWNGRADED;
        }
        $this->customerSubscriptionEventCreator->create($eventType, $subscription->getCustomer(), $subscription);

        if (!$this->stripeBillingEnabled || Customer::BILLING_TYPE_INVOICE === $subscription->getCustomer()->getBillingType()
            || !$this->settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling()
            || null === $subscription->getMainExternalReference()) {
            $this->invoiceSubscriptionManager->changeSubscriptionPlan($subscription, $plan, $price, $billingChangeTiming);

            return;
        }

        $this->stripeBillingManager->changeSubscriptionPlan($subscription, $plan, $price, $billingChangeTiming);
    }
}
