<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Payment;
use Parthenon\Athena\ResultSet;

interface PaymentRepositoryInterface extends \Parthenon\Billing\Repository\PaymentRepositoryInterface
{
    /**
     * @return Payment[]
     */
    public function getPaymentsAmountForCountrySinceDate(string $countryCode, \DateTime $when): array;

    /**
     * @return Payment[]
     */
    public function getPaymentsAmountForStateSinceDate(string $countryCode, string $state, \DateTime $when): array;

    public function getLastTenForCustomer(Customer $customer): ResultSet;

    public function getLatest(int $limit = 5): array;
}
