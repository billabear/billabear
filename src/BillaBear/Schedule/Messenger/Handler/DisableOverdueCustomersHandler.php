<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Background\Invoice\DisableOverdueInvoices;
use BillaBear\Schedule\Messenger\Message\DisableOverdueCustomers;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DisableOverdueCustomersHandler
{
    public function __construct(
        private DisableOverdueInvoices $disableOverdueInvoices,
    ) {
    }

    public function __invoke(DisableOverdueCustomers $checker)
    {
        $this->disableOverdueInvoices->execute();
    }
}
