<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\CachedStats;
use Parthenon\Common\Repository\RepositoryInterface;

interface CachedStatsRepositoryInterface extends RepositoryInterface
{
    public function getNumberStat(string $name): CachedStats;

    public function getMoneyStat(string $name): CachedStats;
}
