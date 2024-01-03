<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
