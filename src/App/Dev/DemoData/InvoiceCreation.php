<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dev\DemoData;

use App\Command\DevDemoDataCommand;
use App\Entity\Customer;
use App\Invoice\InvoiceGenerator;
use App\Payment\InvoiceCharger;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Subscription\Schedule\SchedulerProvider;
use Parthenon\Athena\Filters\GreaterThanFilter;
use Parthenon\Billing\Entity\Subscription;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class InvoiceCreation
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private InvoiceGenerator $invoiceGenerator,
        private InvoiceCharger $invoiceCharger,
        private SchedulerProvider $schedulerProvider,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function createData(OutputInterface $output): void
    {
        $output->writeln("\nCreating invoices");

        $lastId = null;
        $limit = 25;
        $now = new \DateTime('now');
        $startDate = DevDemoDataCommand::getStartDate();
        $brand = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        $progressBar = new ProgressBar($output, $this->subscriptionRepository->getCreatedCountForPeriod($startDate, $now, $brand));
        $progressBar->start();
        do {
            $filter = new GreaterThanFilter();
            $filter->setFieldName('createdAt');
            $filter->setData($startDate);

            $filters = [$filter];
            $result = $this->subscriptionRepository->getList(filters: $filters, limit: $limit, lastId: $lastId);
            $data = $result->getResults();
            $lastId = $result->getLastKey();

            /** @var Subscription $subscription */
            foreach ($data as $subscription) {
                $progressBar->advance();
                if ($subscription->getValidUntil() > $now) {
                    continue;
                }
                do {
                    $lastStart = clone $subscription->getValidUntil();
                    $lastStart->modify('+1 minute');
                    $subscription->setStartOfCurrentPeriod($lastStart);
                    $this->schedulerProvider->getScheduler($subscription->getPrice())->scheduleNextDueDate($subscription);
                    $subscription->setUpdatedAt(new \DateTime('now'));
                    $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($subscription->getCustomer(), [$subscription]);
                    $this->invoiceCharger->chargeInvoice($invoice, createdAt: $subscription->getStartOfCurrentPeriod());
                } while ($subscription->getValidUntil() < $now);
            }
        } while (!empty($data));
        $progressBar->finish();
    }
}
