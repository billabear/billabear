<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\TaxType;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class TaxTypeRepository extends DoctrineCrudRepository implements TaxTypeRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getByName(string $name): TaxType
    {
        $taxType = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$taxType instanceof TaxType) {
            throw new NoEntityFoundException(sprintf('No tax type found for %s', $taxType));
        }

        return $taxType;
    }
}
