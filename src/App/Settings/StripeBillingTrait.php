<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Settings;

use App\Repository\SettingsRepositoryInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait StripeBillingTrait
{
    protected SettingsRepositoryInterface $settingsRepository;

    #[Required]
    public function setSettingRepository(SettingsRepositoryInterface $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function isStripeBillingEnabled(): bool
    {
        return $this->settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling();
    }
}
