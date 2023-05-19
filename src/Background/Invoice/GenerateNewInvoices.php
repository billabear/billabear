<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Background\Invoice;

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
     * @param Subscription $subscription
     *
     * @throws \Exception
     */
    public function generateInvoice(Subscription|array $activeSubscriptions, Customer $customer): void
    {
        foreach ($activeSubscriptions as $activeSubscription) {
            $this->schedulerProvider->getScheduler($activeSubscription->getPrice())->scheduleNextDueDate($activeSubscription);
            $activeSubscription->setUpdatedAt(new \DateTime('now'));
            $this->subscriptionRepository->save($activeSubscription);
        }

        $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $activeSubscriptions);

        if (Customer::BILLING_TYPE_CARD == $customer->getBillingType()) {
            $this->invoiceCharger->chargeInvoice($invoice);
        }
    }
}
