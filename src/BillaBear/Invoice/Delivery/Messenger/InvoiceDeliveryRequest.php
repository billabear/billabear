<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery\Messenger;

/**
 * This name is weird but I can't think of another.
 *
 * There is a free cookie to anyone who can give me a better name!
 */
readonly class InvoiceDeliveryRequest
{
    public function __construct(public string $invoiceId, public string $invoiceDeliveryId)
    {
    }
}
