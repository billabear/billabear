<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
