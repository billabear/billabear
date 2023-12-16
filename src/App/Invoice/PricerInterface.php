<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Entity\Customer;
use App\Enum\TaxType;
use Brick\Money\Money;
use Parthenon\Billing\Entity\Price;

interface PricerInterface
{
    public function getCustomerPriceInfo(Price $price, Customer $customer, TaxType $taxType, int $seatNumber = 1): PriceInfo;

    public function getCustomerPriceInfoFromMoney(Money $money, Customer $customer, bool $includeTax, TaxType $taxType): PriceInfo;
}