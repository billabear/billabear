<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

class RandomInvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(1)).'-'.random_int(100000, 900000);
    }
}