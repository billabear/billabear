<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery\Messenger;

/**
 * This name is weird but I can't think of another.
 *
 * There is a free cookie to anyone who can give me a better name!
 */
class InvoiceDeliveryRequest
{
    public function __construct(public readonly string $invoiceId, public readonly string $invoiceDeliveryId)
    {
    }
}
