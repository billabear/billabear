<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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