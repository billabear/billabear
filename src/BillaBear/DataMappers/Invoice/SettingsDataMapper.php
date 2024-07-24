<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Invoice;

use BillaBear\Dto\Request\App\Invoice\UpdateSettings;
use BillaBear\Dto\Response\App\Invoice\InvoiceSettings as AppDto;
use BillaBear\Entity\Settings\SystemSettings;

class SettingsDataMapper
{
    public function updateInvoiceSettings(UpdateSettings $dto, SystemSettings $settings): SystemSettings
    {
        $settings->setInvoiceNumberGeneration($dto->getInvoiceNumberGeneration());
        $settings->setSubsequentialNumber($dto->getSubsequentialNumber());
        $settings->setDefaultInvoiceDueTime($dto->getDefaultInvoiceDueTime());
        $settings->setInvoiceNumberFormat($dto->getFormat());

        return $settings;
    }

    public function createAppDto(SystemSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setInvoiceNumberGeneration($settings->getInvoiceNumberGeneration());
        $dto->setSubsequentialNumber($settings->getSubsequentialNumber());
        $dto->setDefaultInvoiceDueTime($settings->getDefaultInvoiceDueTime());
        $dto->setFormat($settings->getInvoiceNumberFormat());

        return $dto;
    }
}
