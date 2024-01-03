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

namespace App\Stats\Graphs;

use App\Dto\Response\App\Stats\DashboardStats;
use App\Repository\Stats\PaymentAmountDailyStatsRepositoryInterface;
use App\Repository\Stats\PaymentAmountMonthlyStatsRepositoryInterface;
use App\Repository\Stats\PaymentAmountYearlyStatsRepositoryInterface;

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
