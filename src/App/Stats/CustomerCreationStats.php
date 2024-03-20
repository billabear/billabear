<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats;

use App\Entity\Customer;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\Stats\CustomerCreationDailyStatsRepositoryInterface;
use App\Repository\Stats\CustomerCreationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\CustomerCreationYearlyStatsRepositoryInterface;

class CustomerCreationStats
{
    public function __construct(
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
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
        $brandSettings = $this->brandSettingsRepository->getAll();

        foreach ($brandSettings as $brand) {
            while ($startDate < $now) {
                $endDate = clone $startDate;
                $endDate->modify('+1 day');

                $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->customerCreationDailyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
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

                $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->customerCreationMonthlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
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

                $dayStatCount = $this->customerRepository->getCreatedCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->customerCreationYearlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                $dayStat->setCount($dayStatCount);
                $this->customerCreationYearlyStatsRepository->save($dayStat);
                $startDate = $endDate;
            }
        }
    }

    public function handleStats(Customer $customer)
    {
        $brandCode = $customer->getBrand();

        $dailyStat = $this->customerCreationDailyStatsRepository->getStatForDateTime($customer->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->customerCreationDailyStatsRepository->save($dailyStat);

        $monthlyStat = $this->customerCreationMonthlyStatsRepository->getStatForDateTime($customer->getCreatedAt(), $brandCode);
        $monthlyStat->increaseCount();
        $this->customerCreationMonthlyStatsRepository->save($monthlyStat);

        $yearStat = $this->customerCreationYearlyStatsRepository->getStatForDateTime($customer->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->customerCreationYearlyStatsRepository->save($yearStat);
    }
}
