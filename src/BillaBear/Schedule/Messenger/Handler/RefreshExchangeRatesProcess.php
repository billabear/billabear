<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule\Messenger\Handler;

use BillaBear\Background\Payments\ExchangeRatesFetchProcess;
use BillaBear\Schedule\Messenger\Message\RefreshExchangeRates;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RefreshExchangeRatesProcess
{
    public function __construct(
        private ExchangeRatesFetchProcess $ratesFetchProcess,
    ) {
    }

    public function __invoke(RefreshExchangeRates $exchangeRates)
    {
        $this->ratesFetchProcess->process();
    }
}
