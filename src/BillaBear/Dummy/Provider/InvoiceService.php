<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

use Obol\InvoiceServiceInterface;
use Obol\Model\Invoice\Invoice;

class InvoiceService implements InvoiceServiceInterface
{
    public function fetch(string $id): ?Invoice
    {
        return null;
    }
}
