<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Invoice;

use BillaBear\Database\TransactionManager;
use BillaBear\Entity\Customer;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\Schedule\SchedulerProvider;
use Obol\Exception\PaymentFailureException;
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
        $this->transactionManager->start();
        try {
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
                try {
                    $this->invoiceCharger->chargeInvoice($invoice);
                } catch (PaymentFailureException $e) {
                    $this->getLogger()->info('Tried to charge invoice for customer', ['failure_reason' => $e->getReason()->value]);
                }
            }
        } catch (\Throwable $exception) {
            $this->transactionManager->abort();

            $this->getLogger()->error('An error happened while generating invoice', [
                'exception_message' => $exception->getMessage(),
                'exception_line' => $exception->getLine(),
                'exception_file' => $exception->getTraceAsString(),
                'customer_id' => (string) $customer->getId(),
                'subscriptions' => array_map(function ($item) {
                    return (string) $item->getId();
                }, $activeSubscriptions)]);
        }
    }
}
