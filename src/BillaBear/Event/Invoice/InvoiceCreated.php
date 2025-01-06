<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event\Invoice;

use BillaBear\Entity\Invoice;
use Symfony\Contracts\EventDispatcher\Event;

class InvoiceCreated extends Event
{
    public const string NAME = 'billabear.invoice.created';

    public function __construct(public readonly Invoice $invoice)
    {
    }
}
