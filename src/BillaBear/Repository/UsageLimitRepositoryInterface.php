<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use Parthenon\Common\Repository\RepositoryInterface;

interface UsageLimitRepositoryInterface extends RepositoryInterface
{
    /**
     * @return UsageLimit[]
     */
    public function getForCustomer(Customer $customer): array;

    public function delete(UsageLimit $usageLimit): void;
}
