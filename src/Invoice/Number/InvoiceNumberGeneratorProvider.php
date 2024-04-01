<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Invoice\Number;

use App\Repository\SettingsRepositoryInterface;

class InvoiceNumberGeneratorProvider
{
    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private RandomInvoiceNumberGenerator $randomInvoiceNumberGenerator,
        private SubsequentialGenerator $subsequentialGenerator,
    ) {
    }

    public function getGenerator(): InvoiceNumberGeneratorInterface
    {
        if ('subsequential' === $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getInvoiceNumberGeneration()) {
            return $this->subsequentialGenerator;
        }

        return $this->randomInvoiceNumberGenerator;
    }
}
