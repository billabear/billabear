<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Steps;

use BillaBear\Background\Payments\ExchangeRatesFetchProcess;
use BillaBear\Install\Steps\Tax\TaxDataCreator;

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
