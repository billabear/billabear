<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;

class CancellationRequestRepository extends DoctrineCrudRepository implements CancellationRequestRepositoryInterface
{
    public function getDailyCount(\DateTime $dateTime): array
    {
        $qb = $this->entityRepository->createQueryBuilder('cr');
        $qb->select('COUNT(DISTINCT cr.id) as count')
            ->addSelect('YEAR(cr.createdAt) as yearDate')
            ->addSelect('MONTH(cr.createdAt) as monthDate')
            ->addSelect('DAY(cr.createdAt) as dayDate')
            ->where('cr.createdAt > :dateTime')
            ->groupBy('yearDate')
            ->addGroupBy('monthDate')
            ->addGroupBy('dayDate')
            ->setParameter('dateTime', $dateTime);

        return $qb->getQuery()->getResult();
    }

    public function getMonthlyCount(\DateTime $dateTime): array
    {
        $qb = $this->entityRepository->createQueryBuilder('cr');
        $qb->select('COUNT(DISTINCT cr.id) as count')
            ->addSelect('YEAR(cr.createdAt) as yearDate')
            ->addSelect('MONTH(cr.createdAt) as monthDate')
            ->addSelect('1 as dayDate')
            ->where('cr.createdAt > :dateTime')
            ->groupBy('yearDate')
            ->addGroupBy('monthDate')
            ->setParameter('dateTime', $dateTime);

        return $qb->getQuery()->getResult();
    }

    public function getYearlyCount(\DateTime $dateTime): array
    {
        $qb = $this->entityRepository->createQueryBuilder('cr');
        $qb->select('COUNT(DISTINCT cr.id) as count')
            ->addSelect('YEAR(cr.createdAt) as yearDate')
            ->addSelect('1 as monthDate')
            ->addSelect('1 as dayDate')
            ->where('cr.createdAt > :dateTime')
            ->groupBy('yearDate')
            ->setParameter('dateTime', $dateTime);

        return $qb->getQuery()->getResult();
    }
}
