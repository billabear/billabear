<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountYearlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCreationDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCreationMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCreationYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCreationStats
{
    public function __construct(
        private SubscriptionCreationDailyStatsRepositoryInterface $dailyStatusRepository,
        private SubscriptionCreationMonthlyStatsRepositoryInterface $weeklyStatusRepository,
        private SubscriptionCreationYearlyStatsRepositoryInterface $yearlyStatsRepository,
        private SubscriptionCountDailyStatsRepositoryInterface $countDailyStatsRepository,
        private SubscriptionCountMonthlyStatsRepositoryInterface $countWeeklyStatusRepository,
        private SubscriptionCountYearlyStatsRepositoryInterface $countYearlyStatsRepository,
    ) {
    }

    public function handleStats(Subscription $subscription)
    {
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        $brandCode = $customer->getBrand();

        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->weeklyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->weeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->yearlyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->yearlyStatsRepository->save($yearStat);

        $dailyStat = $this->countDailyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->countWeeklyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->countWeeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->countYearlyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->countYearlyStatsRepository->save($yearStat);
    }
}
