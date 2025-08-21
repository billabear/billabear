<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Import\Stripe\StripeImportProcessor;
use BillaBear\Schedule\Messenger\Message\StripeImport;
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
