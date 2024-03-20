<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Schedule\Messenger\Handler;

use App\Background\UpdateChecker\UpdateChecker;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateCheckerHandler
{
    public function __construct(private UpdateChecker $updateChecker)
    {
    }

    public function __invoke(\App\Schedule\Messenger\Message\UpdateChecker $checker)
    {
        $this->updateChecker->execute();
    }
}
