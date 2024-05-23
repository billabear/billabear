<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\EconomicArea;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class EconomicAreaRepository extends DoctrineCrudRepository implements EconomicAreaRepositoryInterface
{
    public function getByName(string $name): EconomicArea
    {
        $entity = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$entity instanceof EconomicArea) {
            throw new NoEntityFoundException(sprintf("EconomicArea with name '%s' not found.", $name));
        }

        return $entity;
    }
}
