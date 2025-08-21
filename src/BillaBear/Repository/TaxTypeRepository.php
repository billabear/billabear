<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    public function getDefault(): TaxType
    {
        $taxType = $this->entityRepository->findOneBy(['default' => true]);

        if (!$taxType instanceof TaxType) {
            throw new NoEntityFoundException(sprintf('No default tax type'));
        }

        return $taxType;
    }

    public function removeDefault(): void
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('t');
        $queryBuilder->update(TaxType::class, 't')->set('t.default', 'false');
        $queryBuilder->getQuery()->execute();
    }
}
