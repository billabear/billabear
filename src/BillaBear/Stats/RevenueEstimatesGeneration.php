<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

class RevenueEstimatesGeneration
{
    public function __construct(
        private MonthlyRevenueStats $monthlyRevenueStats,
        private YearlyEstimatedRevenueStats $yearlyEstimatedRevenueStats,
    ) {
    }

    public function generate(): void
    {
        $this->monthlyRevenueStats->adjustStats();
        $this->yearlyEstimatedRevenueStats->adjustStats();
    }
}
