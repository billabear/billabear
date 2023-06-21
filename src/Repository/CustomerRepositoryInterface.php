<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
