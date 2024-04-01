<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\Settings;

trait SettingsTrait
{
    protected function getSettings(): Settings
    {
        $settings = $this->settingsRepository->findOneBy(['tag' => Settings::DEFAULT_TAG]);

        if (!$settings instanceof Settings) {
            throw new \Exception('No settings found');
        }

        $this->settingsRepository->getEntityManager()->refresh($settings);

        return $settings;
    }
}
