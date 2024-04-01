<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Schedule\Messenger\Handler;

use App\Background\Generic\GenericTasks;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenericTasksHandler
{
    public function __construct(private GenericTasks $process)
    {
    }

    public function __invoke(\App\Schedule\Messenger\Message\GenericTasks $genericTasks)
    {
        $this->process->execute();
    }
}
