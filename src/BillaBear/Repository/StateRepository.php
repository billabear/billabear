<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\State;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class StateRepository extends DoctrineCrudRepository implements StateRepositoryInterface
{
    public function getByCode(string $code): State
    {
        $state = $this->entityRepository->findOneBy(['code' => $code]);

        if (!$state instanceof State) {
            throw new NoEntityFoundException(sprintf('No state found for %s', $code));
        }

        return $state;
    }
}
