<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Settings;

use BillaBear\Entity\Settings;

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
