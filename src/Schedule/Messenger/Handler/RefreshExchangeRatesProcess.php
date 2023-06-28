<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Schedule\Messenger\Handler;

use App\Background\Payments\ExchangeRatesFetchProcess;
use App\Schedule\Messenger\Message\RefreshExchangeRates;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RefreshExchangeRatesProcess
{
    public function __construct(
        private ExchangeRatesFetchProcess $ratesFetchProcess
    ) {
    }

    public function __invoke(RefreshExchangeRates $exchangeRates)
    {
        $this->ratesFetchProcess->process();
    }
}
