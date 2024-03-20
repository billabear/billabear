<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice;

use App\Tax\TaxInfo;
use Brick\Money\Money;

class PriceInfo
{
    public function __construct(
        public readonly Money $total,
        public readonly Money $subTotal,
        public readonly Money $vat,
        public readonly TaxInfo $taxInfo,
    ) {
    }
}
