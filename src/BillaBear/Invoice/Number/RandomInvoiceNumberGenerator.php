<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Number;

class RandomInvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(1)).'-'.random_int(100000, 900000);
    }
}
