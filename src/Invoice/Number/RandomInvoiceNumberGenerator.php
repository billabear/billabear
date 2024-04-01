<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice\Number;

class RandomInvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(1)).'-'.random_int(100000, 900000);
    }
}
