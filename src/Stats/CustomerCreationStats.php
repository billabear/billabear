<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Stats;

use App\Repository\CustomerRepositoryInterface;
use App\Repository\Stats\CustomerCreationDailyStatsRepositoryInterface;
use App\Repository\Stats\CustomerCreationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\CustomerCreationYearlyStatsRepositoryInterface;

class CustomerCreationStats
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerCreationDailyStatsRepositoryInterface $customerCreationDailyStatsRepository,
        private CustomerCreationMonthlyStatsRepositoryInterface $customerCreationMonthlyStatsRepository,
        private CustomerCreationYearlyStatsRepositoryInterface $customerCreationYearlyStatsRepository,
    ) {
    }

    public function generate()
    {
        $oldestCustomer = $this->customerRepository->getOldestCustomer();
        $now = new \DateTime('now');

        $startDate = clone $oldestCustomer->getCreatedAt();
        if ($startDate instanceof \DateTimeImmutable) {
            $startDate = \DateTime::createFromImmutable($startDate);
        }
        while ($startDate < $now) {
            $endDate = clone $startDate;
            $endDate->modify('+1 day');

            $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate);
            $dayStat = $this->customerCreationDailyStatsRepository->getStatForDateTime($startDate, 'default');
            $dayStat->setCount($dayStatCount);
            $this->customerCreationDailyStatsRepository->save($dayStat);
            $startDate = $endDate;
        }
        $startDate = clone $oldestCustomer->getCreatedAt();
        if ($startDate instanceof \DateTimeImmutable) {
            $startDate = \DateTime::createFromImmutable($startDate);
        }

        while ($startDate < $now) {
            $startDate->modify('first day of this month');
            $endDate = clone $startDate;
            $endDate->modify('+1 month');

            $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate);
            $dayStat = $this->customerCreationMonthlyStatsRepository->getStatForDateTime($startDate, 'default');
            $dayStat->setCount($dayStatCount);
            $this->customerCreationMonthlyStatsRepository->save($dayStat);
            $startDate = $endDate;
        }

        $startDate = clone $oldestCustomer->getCreatedAt();
        if ($startDate instanceof \DateTimeImmutable) {
            $startDate = \DateTime::createFromImmutable($startDate);
        }
        while ($startDate < $now) {
            $startDate->modify('first day of this year');
            $endDate = clone $startDate;
            $endDate->modify('+1 year');

            $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate);
            $dayStat = $this->customerCreationYearlyStatsRepository->getStatForDateTime($startDate, 'default');
            $dayStat->setCount($dayStatCount);
            $this->customerCreationYearlyStatsRepository->save($dayStat);
            $startDate = $endDate;
        }
    }
}
