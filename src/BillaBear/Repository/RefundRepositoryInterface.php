<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use Parthenon\Athena\ResultSet;

interface RefundRepositoryInterface extends \Parthenon\Billing\Repository\RefundRepositoryInterface
{
    public function getTotalCount(): int;

    public function getLastTenForCustomer(Customer $customer): ResultSet;
}
