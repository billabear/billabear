<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use Parthenon\Common\Exception\NoEntityFoundException;

interface CustomerRepositoryInterface extends \Parthenon\Billing\Repository\CustomerRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer;

    public function hasCustomerByEmail(string $email): bool;

    public function getOldestCustomer(): Customer;

    public function getCreatedCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int;
}
