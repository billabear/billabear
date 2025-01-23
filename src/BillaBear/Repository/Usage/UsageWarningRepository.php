<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Usage;

use BillaBear\Entity\UsageLimit;
use Parthenon\Common\Repository\DoctrineRepository;

class UsageWarningRepository extends DoctrineRepository implements UsageWarningRepositoryInterface
{
    public function hasOneForUsageLimitAndDates(UsageLimit $usageLimit, \DateTime $startOfPeriod, \DateTime $endOfPeriod): bool
    {
        $qb = $this->entityRepository->createQueryBuilder('uw');

        $qb->select('COUNT(uw) as warning_count')
            ->where('uw.usageLimit = :usagelimit')
            ->andWhere('uw.startOfPeriod >= :startOfPeriod')
            ->andWhere('uw.endOfPeriod >= :endOfPeriod')
            ->setParameter('usagelimit', $usageLimit)
            ->setParameter('startOfPeriod', $startOfPeriod)
            ->setParameter('endOfPeriod', $endOfPeriod);

        $query = $qb->getQuery();
        $query->execute();
        $result = $query->getResult();

        return 0 !== $result[0]['warning_count'];
    }
}
