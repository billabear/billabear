<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
