<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository\Stats;

use App\Entity\Stats\CachedStats;
use App\Enum\CachedStatsType;
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
