<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Background\Usage\CounterUpdate;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CounterUpdateHandler
{
    public function __construct(private CounterUpdate $process)
    {
    }

    public function __invoke(\BillaBear\Schedule\Messenger\Message\CounterUpdate $beforeChargeWarning)
    {
        $this->process->execute();
    }
}
