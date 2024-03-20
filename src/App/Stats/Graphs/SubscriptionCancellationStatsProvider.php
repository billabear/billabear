<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Stats\Graphs;

use App\Dto\Response\App\Stats\DashboardStats;
use App\Repository\Stats\SubscriptionCancellationDailyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationYearlyStatsRepositoryInterface;

class SubscriptionCancellationStatsProvider
{
    public function __construct(
        private SubscriptionCancellationDailyStatsRepositoryInterface $subscriptionCancellationDailyStatsRepository,
        private SubscriptionCancellationMonthlyStatsRepositoryInterface $subscriptionCancellationMonthlyStatsRepository,
        private SubscriptionCancellationYearlyStatsRepositoryInterface $subscriptionCancellationYearlyStatsRepository,
        private StatOutputConverter $statOutputConverter,
    ) {
    }

    public function getMainDashboard(): DashboardStats
    {
        $now = new \DateTime();
        $thirtyDaysAgo = new \DateTime('-29 days');
        $oneYear = new \DateTime('first day of this month');
        $oneYear = $oneYear->modify('-11 months');
        $tenYears = new \DateTime('first day of january');
        $tenYears = $tenYears->modify('-9 years');

        $daily = $this->subscriptionCancellationDailyStatsRepository->getFromToStats($thirtyDaysAgo, $now);
        $monthly = $this->subscriptionCancellationMonthlyStatsRepository->getFromToStats($oneYear, $now);
        $yearly = $this->subscriptionCancellationYearlyStatsRepository->getFromToStats($tenYears, $now);

        $stats = new DashboardStats();
        $stats->setDaily($this->statOutputConverter->convertToDailyOutput($thirtyDaysAgo, $now, $daily));
        $stats->setMonthly($this->statOutputConverter->convertToMonthOutput($oneYear, $now, $monthly));
        $stats->setYearly($this->statOutputConverter->convertToYearOutput($tenYears, $now, $yearly));

        return $stats;
    }
}
