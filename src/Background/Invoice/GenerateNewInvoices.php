<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Invoice;

use App\Database\TransactionManager;
use App\Entity\Customer;
use App\Invoice\InvoiceGenerator;
use App\Payment\InvoiceCharger;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Subscription\Schedule\SchedulerProvider;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\LoggerAwareTrait;

class GenerateNewInvoices
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private InvoiceGenerator $invoiceGenerator,
        private SchedulerProvider $schedulerProvider,
        private InvoiceCharger $invoiceCharger,
        private SettingsRepositoryInterface $settingsRepository,
        private TransactionManager $transactionManager,
    ) {
    }

    public function execute(): void
    {
        $defaultSettings = $this->settingsRepository->getDefaultSettings();

        if (!$defaultSettings->getSystemSettings()->isUseStripeBilling()) {
            $subscriptions = $this->subscriptionRepository->getSubscriptionsExpiringInNextFiveMinutes();
        } else {
            $subscriptions = $this->subscriptionRepository->getInvoiceSubscriptionsExpiringInNextFiveMinutes();
        }

        $customer = null;
        /** @var Subscription $activeSubscriptions */
        $activeSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if ($customer instanceof Customer && $subscription->getCustomer()->getId() != $customer->getId()) {
                $this->generateInvoice($activeSubscriptions, $customer);
                $activeSubscriptions = [];
            }
            $customer = $subscription->getCustomer();
            $activeSubscriptions[] = $subscription;
        }

        if (!$customer instanceof Customer) {
            $this->getLogger()->info('No subscriptions found');

            return;
        }
        $this->generateInvoice($activeSubscriptions, $customer);
    }

    /**
     * @throws \Exception
     */
    protected function generateInvoice(Subscription|array $activeSubscriptions, Customer $customer): void
    {
        try {
            $this->transactionManager->start();
            $this->getLogger()->info('Generating invoice for customer and subscriptions', ['customer_id' => (string) $customer->getId(), 'subscriptions' => array_map(function ($item) {
                return (string) $item->getId();
            }, $activeSubscriptions)]);
            foreach ($activeSubscriptions as $activeSubscription) {
                $this->schedulerProvider->getScheduler($activeSubscription->getPrice())->scheduleNextDueDate($activeSubscription);
                $activeSubscription->setUpdatedAt(new \DateTime('now'));
                $this->subscriptionRepository->save($activeSubscription);
            }
            $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $activeSubscriptions);
            $this->transactionManager->finish();
            if (Customer::BILLING_TYPE_CARD == $customer->getBillingType()) {
                $this->invoiceCharger->chargeInvoice($invoice);
            }
        } catch (\Throwable $exception) {
            $this->transactionManager->abort();

            $this->getLogger()->error('An error happened while generating exception', [
                'exception_message' => $exception->getMessage(),
                'customer_id' => (string) $customer->getId(),
                'subscriptions' => array_map(function ($item) {
                    return (string) $item->getId();
                }, $activeSubscriptions)]);
        }
    }
}
