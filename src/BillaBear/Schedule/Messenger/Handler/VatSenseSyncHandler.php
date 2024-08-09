<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Tax\VatSense\TaxSyncer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VatSenseSyncHandler
{
    public function __construct(private TaxSyncer $process)
    {
    }

    public function __invoke(\BillaBear\Schedule\Messenger\Message\VatSenseSync $genericTasks)
    {
        $this->process->process();
    }
}
