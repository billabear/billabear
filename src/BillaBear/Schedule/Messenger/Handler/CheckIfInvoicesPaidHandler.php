<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Background\Invoice\CheckIfPaid;
use BillaBear\Schedule\Messenger\Message\CheckIfInvoicesPaid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CheckIfInvoicesPaidHandler
{
    public function __construct(private CheckIfPaid $checkIfPaid)
    {
    }

    public function __invoke(CheckIfInvoicesPaid $checkIfInvoicesPaid)
    {
        $this->checkIfPaid->execute();
    }
}
