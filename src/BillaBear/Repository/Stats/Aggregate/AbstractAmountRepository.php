<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

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
