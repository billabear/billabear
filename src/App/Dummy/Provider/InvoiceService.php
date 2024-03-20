<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Dummy\Provider;

use Obol\InvoiceServiceInterface;
use Obol\Model\Invoice\Invoice;

class InvoiceService implements InvoiceServiceInterface
{
    public function fetch(string $id): ?Invoice
    {
        return null;
    }
}
