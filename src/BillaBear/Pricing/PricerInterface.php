<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\TaxType;
use Brick\Money\Money;

interface PricerInterface
{
    /**
     * @return PriceInfo[]
     */
    public function getCustomerPriceInfo(
        Price $price,
        Customer $customer,
        TaxType $taxType,
        null|float|int $seatNumber = 1,
        null|float|int $alreadyBilled = null,
    ): array;

    public function getCustomerPriceInfoFromMoney(Money $money, Customer $customer, bool $includeTax, ?TaxType $taxType): PriceInfo;
}
