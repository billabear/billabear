<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
