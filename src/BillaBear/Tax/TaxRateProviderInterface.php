<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Product;
use BillaBear\Entity\TaxType;
use Brick\Money\Money;

interface TaxRateProviderInterface
{
    public function getRateForCustomer(Customer $customer, ?TaxType $taxType, ?Product $product = null, ?Money $amount = null): TaxInfo;
}
