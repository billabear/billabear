<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\SubscriptionCancellationDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCancellationMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCancellationYearlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionCountYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCancellationStats
{
    public function __construct(
        private SubscriptionCancellationDailyStatsRepositoryInterface $dailyStatusRepository,
        private SubscriptionCancellationMonthlyStatsRepositoryInterface $weeklyStatusRepository,
        private SubscriptionCancellationYearlyStatsRepositoryInterface $yearlyStatsRepository,
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

        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->weeklyStatusRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->weeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->yearlyStatsRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->yearlyStatsRepository->save($yearStat);

        $dailyStat = $this->countDailyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $dailyStat->decreaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->countWeeklyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $weeklyStat->decreaseCount();
        $this->countWeeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->countYearlyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $yearStat->decreaseCount();
        $this->countYearlyStatsRepository->save($yearStat);
    }
}
