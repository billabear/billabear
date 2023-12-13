<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
