<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountYearlyStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;

class CreateSubscriptionCountStats
{
    public function __construct(
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionCountDailyStatsRepositoryInterface $subscriptionCountDailyStatsRepository,
        private SubscriptionCountMonthlyStatsRepositoryInterface $subscriptionCountMonthlyStatsRepository,
        private SubscriptionCountYearlyStatsRepositoryInterface $subscriptionCountYearlyStatsRepository,
    ) {
    }

    public function generate()
    {
        $oldestSubscription = $this->subscriptionRepository->getOldestSubscription();
        $now = new \DateTime('now');

        $startDate = clone $oldestSubscription->getCreatedAt();
        if ($startDate instanceof \DateTimeImmutable) {
            $startDate = \DateTime::createFromImmutable($startDate);
        }
        $brands = $this->brandSettingsRepository->getAll();

        foreach ($brands as $brand) {
            while ($startDate < $now) {
                $endDate = clone $startDate;
                $endDate->modify('+1 day');

                $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->subscriptionCountDailyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                $dayStat->setCount($dayStatCount);
                $this->subscriptionCountDailyStatsRepository->save($dayStat);
                $startDate = $endDate;
            }
            $startDate = clone $oldestSubscription->getCreatedAt();
            if ($startDate instanceof \DateTimeImmutable) {
                $startDate = \DateTime::createFromImmutable($startDate);
            }

            while ($startDate < $now) {
                $startDate->modify('first day of this month');
                $endDate = clone $startDate;
                $endDate->modify('+1 month');

                $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->subscriptionCountMonthlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                $dayStat->setCount($dayStatCount);
                $this->subscriptionCountMonthlyStatsRepository->save($dayStat);
                $startDate = $endDate;
            }

            $startDate = clone $oldestSubscription->getCreatedAt();
            if ($startDate instanceof \DateTimeImmutable) {
                $startDate = \DateTime::createFromImmutable($startDate);
            }
            while ($startDate < $now) {
                $startDate->modify('first day of this year');
                $endDate = clone $startDate;
                $endDate->modify('+1 year');

                $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                $dayStat = $this->subscriptionCountYearlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                $dayStat->setCount($dayStatCount);
                $this->subscriptionCountYearlyStatsRepository->save($dayStat);
                $startDate = $endDate;
            }
        }
    }
}
