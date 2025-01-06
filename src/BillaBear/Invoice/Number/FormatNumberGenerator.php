<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Number;

use BillaBear\Repository\SettingsRepositoryInterface;

class FormatNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function generate(): string
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $count = $settings->getSystemSettings()->getSubsequentialNumber();
        ++$count;
        $settings->getSystemSettings()->setSubsequentialNumber($count);
        $this->settingsRepository->save($settings);

        $format = $settings->getSystemSettings()->getInvoiceNumberFormat();
        $format = str_replace('%S', $count, $format);

        $randomString = bin2hex(random_bytes(4));

        return str_replace('%R', $randomString, $format);
    }
}
