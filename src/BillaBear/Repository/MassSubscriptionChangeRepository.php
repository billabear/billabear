<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;

class MassSubscriptionChangeRepository extends DoctrineCrudRepository implements MassSubscriptionChangeRepositoryInterface
{
    public function findWithinFiveMinutes(\DateTime $dateTime): array
    {
        $start = clone $dateTime;
        $start = $start->modify('-1 minute');

        $end = clone $dateTime;
        $end = $end->modify('+5 minutes');

        $qb = $this->entityRepository->createQueryBuilder('ms');
        $qb->where('ms.changeDate > :startDateTime')
            ->andWhere('ms.changeDate < :endDateTime')
            ->setParameter(':startDateTime', $start)
            ->setParameter(':endDateTime', $end);

        return $qb->getQuery()->execute();
    }
}
