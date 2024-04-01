<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository\Stats;

use Parthenon\Common\Repository\DoctrineRepository;

abstract class AbstractAmountRepository extends DoctrineRepository implements AmountRepositoryInterface
{
    public function getFromToStats(\DateTime $start, \DateTime $end): array
    {
        $qb = $this->entityRepository->createQueryBuilder('a');
        $qb->where('a.date >= :startDate')
            ->andWhere('a.date <= :endDate')
            ->setParameter(':startDate', $start)
            ->setParameter(':endDate', $end);
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
