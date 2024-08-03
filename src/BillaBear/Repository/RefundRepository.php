<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use Parthenon\Athena\ResultSet;

class RefundRepository extends \Parthenon\Billing\Repository\Orm\RefundRepository implements RefundRepositoryInterface
{
    public function getLastTenForCustomer(Customer $customer): ResultSet
    {
        $results = $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC'], 11);

        return new ResultSet($results, 'createdAt', 'DESC', 10);
    }
}
