<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Stats;

class DashboardStats
{
    private array $daily = [];

    private array $monthly = [];

    private array $yearly = [];

    public function getDaily(): array
    {
        return $this->daily;
    }

    public function setDaily(array $daily): void
    {
        $this->daily = $daily;
    }

    public function getMonthly(): array
    {
        return $this->monthly;
    }

    public function setMonthly(array $monthly): void
    {
        $this->monthly = $monthly;
    }

    public function getYearly(): array
    {
        return $this->yearly;
    }

    public function setYearly(array $yearly): void
    {
        $this->yearly = $yearly;
    }
}
