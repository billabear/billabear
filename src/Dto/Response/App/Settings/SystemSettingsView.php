<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SystemSettingsView
{
    #[SerializedName('system_settings')]
    private SystemSettings $systemSettings;

    private array $timezones = [];

    public function getSystemSettings(): SystemSettings
    {
        return $this->systemSettings;
    }

    public function setSystemSettings(SystemSettings $systemSettings): void
    {
        $this->systemSettings = $systemSettings;
    }

    public function getTimezones(): array
    {
        return $this->timezones;
    }

    public function setTimezones(array $timezones): void
    {
        $this->timezones = $timezones;
    }
}
