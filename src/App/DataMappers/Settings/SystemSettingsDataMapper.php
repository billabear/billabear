<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\SystemSettings as RequestDto;
use App\Dto\Response\App\Settings\SystemSettings as AppDto;
use App\Entity\Settings\SystemSettings;

class SystemSettingsDataMapper
{
    public function updateEntity(RequestDto $dto, SystemSettings $settings): SystemSettings
    {
        $settings->setSystemUrl($dto->getSystemUrl());
        $settings->setTimezone($dto->getTimezone());
        $settings->setInvoiceNumberGeneration($dto->getInvoiceNumberGeneration());
        $settings->setSubsequentialNumber($dto->getSubsequentialNumber());
        $settings->setDefaultInvoiceDueTime($dto->getDefaultInvoiceDueTime());

        return $settings;
    }

    public function createAppDto(SystemSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setSystemUrl($settings->getSystemUrl());
        $dto->setTimezone($settings->getTimezone());
        $dto->setInvoiceNumberGeneration($settings->getInvoiceNumberGeneration());
        $dto->setSubsequentialNumber($settings->getSubsequentialNumber());
        $dto->setDefaultInvoiceDueTime($settings->getDefaultInvoiceDueTime());

        return $dto;
    }
}
