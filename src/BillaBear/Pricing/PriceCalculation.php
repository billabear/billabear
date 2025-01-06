<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing;

use Brick\Money\Money;

readonly class PriceCalculation
{
    public function __construct(
        public Money $money,
        public float|int $quantity,
        public Money $netPrice,
    ) {
    }
}
