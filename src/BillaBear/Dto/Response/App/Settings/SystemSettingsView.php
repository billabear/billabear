<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Settings;

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
