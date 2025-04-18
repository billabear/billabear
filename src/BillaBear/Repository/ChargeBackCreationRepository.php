<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Parthenon\Athena\Repository\DoctrineCrudRepository;

class ChargeBackCreationRepository extends DoctrineCrudRepository implements ChargeBackCreationRepositoryInterface
{
    public function getFailedProcesses(): \Generator
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('cbc');
        $queryBuilder->select('cbc');
        $queryBuilder->where('cbc.hasError = true');
        $query = $queryBuilder->getQuery();

        $query->execute();
        foreach ($query->toIterable() as $result) {
            yield $result;
        }
    }
}
