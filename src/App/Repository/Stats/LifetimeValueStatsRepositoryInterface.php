<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Repository\Stats;

interface LifetimeValueStatsRepositoryInterface
{
    public function getLifetimeValue(array $filters = []): array;

    public function getAverageLifespan(array $filters = []): float;

    public function getPaymentTotals(array $filters = []): array;

    public function getUniqueCustomerCount(array $filters = []): int;
}
