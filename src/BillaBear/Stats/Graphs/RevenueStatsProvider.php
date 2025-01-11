<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats\Graphs;

use BillaBear\Dto\Response\App\Stats\DashboardStats;
use BillaBear\Repository\Stats\PaymentStatsRepositoryInterface;
use BillaBear\Stats\Graphs\Formatters\StackedColumns;

class RevenueStatsProvider
{
    public function __construct(
        private PaymentStatsRepositoryInterface $paymentStatsRepository,
        private StackedColumns $stackedColumns,
    ) {
    }

    public function getMainDashboard(): DashboardStats
    {
        $daily = $this->paymentStatsRepository->getDailyPaymentStatesForAMonth();
        $monthly = $this->paymentStatsRepository->getMonthlyPaymentStatsForAYear();
        $yearly = $this->paymentStatsRepository->getYearlyPaymentStats();

        $stats = new DashboardStats();
        $stats->setDaily($this->stackedColumns->formatDaily($daily));
        $stats->setMonthly($this->stackedColumns->formatMonthly($monthly));
        $stats->setYearly($this->stackedColumns->formatYearly($yearly));

        return $stats;
    }
}
