<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Settings;

use BillaBear\Repository\SettingsRepositoryInterface;
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
