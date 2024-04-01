<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Schedule\Messenger\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateNewInvoices
{
    public function __construct(private \App\Background\Invoice\GenerateNewInvoices $process)
    {
    }

    public function __invoke(\App\Schedule\Messenger\Message\GenerateNewInvoices $generateNewInvoices)
    {
        $this->process->execute();
    }
}
