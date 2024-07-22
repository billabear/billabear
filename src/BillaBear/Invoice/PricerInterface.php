<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\TaxType;
use Brick\Money\Money;

interface PricerInterface
{
    public function getCustomerPriceInfo(Price $price, Customer $customer, TaxType $taxType, int $seatNumber = 1): PriceInfo;

    public function getCustomerPriceInfoFromMoney(Money $money, Customer $customer, bool $includeTax, ?TaxType $taxType): PriceInfo;
}
