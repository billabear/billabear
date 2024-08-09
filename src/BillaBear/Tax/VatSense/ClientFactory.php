<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\VatSense;

use BillaBear\Repository\SettingsRepositoryInterface;

class ClientFactory
{
    public function __construct(private SettingsRepositoryInterface $settings)
    {
    }

    public function build(): VatSenseClient
    {
        $apiKey = $this->settings->getDefaultSettings()->getTaxSettings()->getVatSenseApiKey();
        var_dump($apiKey);
        exit;

        return new VatSenseClient($apiKey);
    }
}
