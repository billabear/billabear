<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats\Graphs;

use BillaBear\Dto\Response\App\Stats\DashboardStats;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\PaymentAmountYearlyStatsRepositoryInterface;
use BillaBear\Stats\Graphs\Formatters\MoneyStatOutputConverter;

class PaymentAmountStatsProvider
{
    public function __construct(
        private PaymentAmountDailyStatsRepositoryInterface $paymentAmountDailyStatsRepository,
        private PaymentAmountMonthlyStatsRepositoryInterface $paymentAmountMonthlyStatsRepository,
        private PaymentAmountYearlyStatsRepositoryInterface $paymentAmountYearlyStatsRepository,
        private MoneyStatOutputConverter $statOutputConverter,
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

        $daily = $this->paymentAmountDailyStatsRepository->getFromToStats($thirtyDaysAgo, $now);
        $monthly = $this->paymentAmountMonthlyStatsRepository->getFromToStats($oneYear, $now);
        $yearly = $this->paymentAmountYearlyStatsRepository->getFromToStats($tenYears, $now);

        $stats = new DashboardStats();
        $stats->setDaily($this->statOutputConverter->convertToDailyOutput($thirtyDaysAgo, $now, $daily));
        $stats->setMonthly($this->statOutputConverter->convertToMonthOutput($oneYear, $now, $monthly));
        $stats->setYearly($this->statOutputConverter->convertToYearOutput($tenYears, $now, $yearly));

        return $stats;
    }
}
