<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Schedule\Messenger\Handler;

use App\Background\ExpiringCards\StartProcess;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExpiredCardsFirstOfMonth
{
    public function __construct(private StartProcess $process)
    {
    }

    public function __invoke(\App\Schedule\Messenger\Message\ExpiredCardsFirstOfMonth $cardsFirstOfMonth)
    {
        $this->process->execute();
    }
}