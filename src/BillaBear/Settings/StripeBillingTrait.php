<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Settings;

use BillaBear\Repository\SettingsRepositoryInterface;
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
