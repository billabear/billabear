<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Install\Steps;

use App\Background\Payments\ExchangeRatesFetchProcess;
use App\Install\Steps\Tax\TaxDataCreator;

class DataStep
{
    public function __construct(
        private ExchangeRatesFetchProcess $exchangeRatesFetchProcess,
        private TaxDataCreator $taxDataCreator,
    ) {
    }

    public function install()
    {
        $this->exchangeRatesFetchProcess->process();
        $this->taxDataCreator->process();
    }
}
