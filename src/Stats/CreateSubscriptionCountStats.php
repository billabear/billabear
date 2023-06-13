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

use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\Stats\SubscriptionCountDailyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCountMonthlyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCountYearlyStatsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;

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
