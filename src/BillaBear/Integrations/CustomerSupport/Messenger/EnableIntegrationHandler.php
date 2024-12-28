<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Messenger;

use BillaBear\Integrations\CustomerSupport\Action\Setup;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EnableIntegrationHandler
{
    public function __construct(private Setup $setup)
    {
    }

    public function __invoke(EnableIntegration $enableIntegration): void
    {
        $this->setup->setup();
    }
}
