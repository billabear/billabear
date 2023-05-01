<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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