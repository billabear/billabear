<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Settings;

use App\Repository\SettingsRepositoryInterface;
use Parthenon\Common\Config\SiteUrlProviderInterface;

class SiteUrlProvider implements SiteUrlProviderInterface
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function getSiteUrl(): string
    {
        return $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getSystemUrl();
    }
}
