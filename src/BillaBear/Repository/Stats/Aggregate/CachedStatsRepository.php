<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Stats\CachedStatsType;
use Parthenon\Common\Repository\DoctrineRepository;

class CachedStatsRepository extends DoctrineRepository implements CachedStatsRepositoryInterface
{
    public function getNumberStat(string $name): CachedStats
    {
        $stat = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$stat instanceof CachedStats) {
            $stat = new CachedStats();
            $stat->setName($name);
            $stat->setType(CachedStatsType::NUMBER);
            $stat->setValue(0);
        }

        return $stat;
    }

    public function getMoneyStat(string $name): CachedStats
    {
        $stat = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$stat instanceof CachedStats) {
            $stat = new CachedStats();
            $stat->setName($name);
            $stat->setType(CachedStatsType::MONEY);
            $stat->setValue(0);
        }

        return $stat;
    }
}
