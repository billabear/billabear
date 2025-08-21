<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\StripeImport;
use Parthenon\Common\Repository\DoctrineRepository;

class StripeImportRepository extends DoctrineRepository implements StripeImportRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function findActive(): ?StripeImport
    {
        return $this->entityRepository->findOneBy(['complete' => false]);
    }
}
