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
