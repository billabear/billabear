<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Schedule\Messenger\Handler;

use App\Import\Stripe\StripeImportProcessor;
use App\Schedule\Messenger\Message\StripeImport;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StripeImportHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private StripeImportProcessor $importProcessor,
    ) {
    }

    public function __invoke(StripeImport $stripeImport)
    {
        $this->getLogger()->info('Stripe Import scheduler task');
        $this->importProcessor->process();
    }
}
