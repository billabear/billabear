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

namespace App\Factory;

use App\Dto\Request\App\Settings\SystemSettings as RequestDto;
use App\Dto\Response\App\Settings\SystemSettings as AppDto;
use App\Entity\Settings\SystemSettings;

class SystemSettingsFactory
{
    public function updateEntity(RequestDto $dto, SystemSettings $settings): SystemSettings
    {
        $settings->setWebhookUrl($dto->getWebhookUrl());
        $settings->setTimezone($dto->getTimezone());

        return $settings;
    }

    public function createAppDto(SystemSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setWebhookUrl($settings->getWebhookUrl());
        $dto->setTimezone($settings->getTimezone());

        return $dto;
    }
}
