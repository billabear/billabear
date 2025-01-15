<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Install\Upgrade;

use BillaBear\Install\Steps\Tax\DataProvider;
use BillaBear\Repository\CountryRepositoryInterface;

readonly class CountryUpdater
{
    public function __construct(
        private DataProvider $dataProvider,
        private CountryRepositoryInterface $countryRepository,
    ) {
    }

    public function execute(): void
    {
    }
}
