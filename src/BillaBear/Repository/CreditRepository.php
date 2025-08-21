<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use Parthenon\Athena\Repository\DoctrineCrudRepository;

class CreditRepository extends DoctrineCrudRepository implements CreditRepositoryInterface
{
    public function getForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }

    public function getTotalCount(): int
    {
        return $this->entityRepository->count([]);
    }
}
