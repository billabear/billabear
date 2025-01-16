<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats\Graphs;

use BillaBear\Dto\Response\App\Stats\DashboardStats;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountYearlyStatsRepositoryInterface;
use BillaBear\Stats\Graphs\Formatters\StatOutputConverter;

class SubscriptionCountStatsProvider
{
    public function __construct(
        private SubscriptionCountDailyStatsRepositoryInterface $subscriptionCountDailyStatsRepository,
        private SubscriptionCountMonthlyStatsRepositoryInterface $subscriptionCountMonthlyStatsRepository,
        private SubscriptionCountYearlyStatsRepositoryInterface $subscriptionCountYearlyStatsRepository,
        private StatOutputConverter $statOutputConverter,
    ) {
    }

    public function getMainDashboard(): DashboardStats
    {
        $now = new \DateTime();
        $thirtyDaysAgo = new \DateTime('-29 days');
        $thirtyDaysAgo = $thirtyDaysAgo->modify('midnight');
        $oneYear = new \DateTime('first day of this month');
        $oneYear = $oneYear->modify('-11 months');
        $tenYears = new \DateTime('first day of january');
        $tenYears = $tenYears->modify('-9 years');

        $daily = $this->subscriptionCountDailyStatsRepository->getFromToStats($thirtyDaysAgo, $now);
        $monthly = $this->subscriptionCountMonthlyStatsRepository->getFromToStats($oneYear, $now);
        $yearly = $this->subscriptionCountYearlyStatsRepository->getFromToStats($tenYears, $now);

        $stats = new DashboardStats();
        $stats->setDaily($this->statOutputConverter->convertToDailyOutput($thirtyDaysAgo, $now, $daily, true));
        $stats->setMonthly($this->statOutputConverter->convertToMonthOutput($oneYear, $now, $monthly, true));
        $stats->setYearly($this->statOutputConverter->convertToYearOutput($tenYears, $now, $yearly, true));

        return $stats;
    }
}
