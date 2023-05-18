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
        private SchedulerProvider $schedulerProvider
    ) {
    }

    public function execute(): void
    {
        $subscriptions = $this->subscriptionRepository->getSubscriptionsExpiringInNextFiveMinutes();

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

        $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $activeSubscriptions);
    }
}
