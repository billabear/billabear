<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    public function getPaymentsCountSinceDate(string $countryCode, \DateTime $when): int;

    /**
     * @return Payment[]
     */
    public function getPaymentsAmountForStateSinceDate(string $countryCode, string $state, \DateTime $when): array;

    public function getPaymentsCountForStateSinceDate(string $countryCode, string $state, \DateTime $when): int;

    public function getLastTenForCustomer(Customer $customer): ResultSet;

    public function getLatest(int $limit = 10): array;

    public function getTotalCount(): int;
}
