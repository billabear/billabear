<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Request\App\Settings\SystemSettings as RequestDto;
use BillaBear\Dto\Response\App\Settings\SystemSettings as AppDto;
use BillaBear\Entity\Settings\SystemSettings;

class SystemSettingsDataMapper
{
    public function updateEntity(RequestDto $dto, SystemSettings $settings): SystemSettings
    {
        $settings->setSystemUrl($dto->systemUrl);
        $settings->setTimezone($dto->timezone);

        return $settings;
    }

    public function createAppDto(SystemSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setSystemUrl($settings->getSystemUrl());
        $dto->setTimezone($settings->getTimezone());

        return $dto;
    }
}
