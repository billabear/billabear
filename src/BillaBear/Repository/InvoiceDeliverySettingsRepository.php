<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use Parthenon\Common\Repository\DoctrineRepository;

class InvoiceDeliverySettingsRepository extends DoctrineRepository implements InvoiceDeliverySettingsRepositoryInterface
{
    public function getAllForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }

    public function getEnabledForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer, 'enabled' => true]);
    }
}
