<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
