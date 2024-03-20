<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

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
