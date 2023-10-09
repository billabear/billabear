<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
