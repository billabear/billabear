<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Invoice;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Database\TransactionManager;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Exception\Invoice\NothingToInvoiceException;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\CustomerSubscriptionEventCreator;
use BillaBear\Subscription\Schedule\SchedulerProvider;
use BillaBear\Subscription\TrialManager;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Common\LoggerAwareTrait;

class GenerateNewInvoices
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly InvoiceGenerator $invoiceGenerator,
        private readonly SchedulerProvider $schedulerProvider,
        private readonly InvoiceCharger $invoiceCharger,
        private readonly SettingsRepositoryInterface $settingsRepository,
        private readonly TransactionManager $transactionManager,
        private readonly TrialManager $trialEnder,
        private readonly CustomerSubscriptionEventCreator $customerSubscriptionEventCreator,
    ) {
    }

    public function execute(): void
    {
        $now = new \DateTime();

        $this->getLogger()->info('Starting to generate new invoices');
        $defaultSettings = $this->settingsRepository->getDefaultSettings();

        if (!$defaultSettings->getSystemSettings()->isUseStripeBilling()) {
            $subscriptions = $this->subscriptionRepository->getSubscriptionsExpiringInNextFiveMinutes();
        } else {
            $subscriptions = $this->subscriptionRepository->getInvoiceSubscriptionsExpiringInNextFiveMinutes();
        }

        $customer = null;
        /** @var Subscription[] $activeSubscriptions */
        $activeSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if ($customer instanceof Customer && $subscription->getCustomer()->getId() != $customer->getId()) {
                $this->generateInvoice($activeSubscriptions, $customer);
                $activeSubscriptions = [];
            }

            $customer = $subscription->getCustomer();
            /** @var SubscriptionPlan $subscriptionPlan */
            $subscriptionPlan = $subscription->getSubscriptionPlan();

            if ($subscriptionPlan->getIsTrialStandalone() && SubscriptionStatus::TRIAL_ACTIVE === $subscription->getStatus()) {
                $this->getLogger()->info('Skipping subscription that is for a standalone trial', ['subscription_id' => (string) $subscription->getId()]);
                if ($subscription->getValidUntil() < $now) {
                    $this->trialEnder->endTrial($subscription);
                }
                continue;
            }
            $activeSubscriptions[] = $subscription;
        }

        if (!$customer instanceof Customer) {
            $this->getLogger()->info('No subscriptions found');

            return;
        }

        if (0 === count($activeSubscriptions)) {
            return;
        }
        $this->generateInvoice($activeSubscriptions, $customer);
    }

    /**
     * @param Subscription[] $activeSubscriptions
     */
    protected function generateInvoice(array|Subscription $activeSubscriptions, Customer $customer): void
    {
        $this->transactionManager->start();
        try {
            $this->getLogger()->info('Generating invoice for customer and subscriptions', ['customer_id' => (string) $customer->getId(), 'subscriptions' => array_map(function ($item) {
                return (string) $item->getId();
            }, $activeSubscriptions)]);
            foreach ($activeSubscriptions as $activeSubscription) {
                $this->schedulerProvider->getScheduler($activeSubscription->getPrice())->scheduleNextDueDate($activeSubscription);
                $activeSubscription->setUpdatedAt(new \DateTime('now'));
                if (SubscriptionStatus::TRIAL_ACTIVE == $activeSubscription->getStatus()) {
                    $this->customerSubscriptionEventCreator->create(
                        CustomerSubscriptionEventType::TRIAL_CONVERTED,
                        $activeSubscription->getCustomer(),
                        $activeSubscription
                    );
                }
                $activeSubscription->setStatus(SubscriptionStatus::ACTIVE);
                $this->subscriptionRepository->save($activeSubscription);
            }
            try {
                $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $activeSubscriptions);
            } catch (NothingToInvoiceException) {
                $this->transactionManager->finish();

                return;
            }

            $this->transactionManager->finish();
            if (Customer::BILLING_TYPE_CARD == $customer->getBillingType()) {
                try {
                    $this->invoiceCharger->chargeInvoice($invoice);
                } catch (PaymentFailureException $e) {
                    $this->getLogger()->info('Tried to charge invoice for customer', ['failure_reason' => $e->getReason()->value]);
                }
            }
        } catch (\Throwable $exception) {
            $this->getLogger()->critical('An error happened while generating invoice', [
                'exception_message' => $exception->getMessage(),
                'exception_line' => $exception->getLine(),
                'exception_file' => $exception->getFile(),
                'customer_id' => (string) $customer->getId(),
                'subscriptions' => array_map(function ($item) {
                    return (string) $item->getId();
                }, $activeSubscriptions)]);

            $this->transactionManager->abort();
        }
    }
}
