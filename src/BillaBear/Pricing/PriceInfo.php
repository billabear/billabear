<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing;

use BillaBear\Tax\TaxInfo;
use Brick\Money\Money;

readonly class PriceInfo
{
    public function __construct(
        public Money $total,
        public Money $subTotal,
        public Money $vat,
        public TaxInfo $taxInfo,
        public float $quantity,
        public Money $netPrice,
    ) {
    }
}
