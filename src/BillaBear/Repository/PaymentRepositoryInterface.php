<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

interface PaymentRepositoryInterface extends \Parthenon\Billing\Repository\PaymentRepositoryInterface
{
    public function getPaymentsAmountForCountrySinceDate(string $countryCode, \DateTime $when): array;
}