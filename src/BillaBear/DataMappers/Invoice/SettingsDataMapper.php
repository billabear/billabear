<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Invoice;

use BillaBear\Dto\Request\App\Invoice\UpdateSettings;
use BillaBear\Dto\Response\App\Invoice\InvoiceSettings as AppDto;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Invoice\InvoiceGenerationType;

class SettingsDataMapper
{
    public function updateInvoiceSettings(UpdateSettings $dto, SystemSettings $settings): SystemSettings
    {
        $settings->setInvoiceNumberGeneration($dto->getInvoiceNumberGeneration());
        $settings->setSubsequentialNumber($dto->getSubsequentialNumber());
        $settings->setDefaultInvoiceDueTime($dto->getDefaultInvoiceDueTime());
        $settings->setInvoiceNumberFormat($dto->getFormat());
        $settings->setInvoiceGenerationType(InvoiceGenerationType::from($dto->getInvoiceGeneration()));

        return $settings;
    }

    public function createAppDto(SystemSettings $settings): AppDto
    {
        $dto = new AppDto();
        $dto->setInvoiceNumberGeneration($settings->getInvoiceNumberGeneration());
        $dto->setSubsequentialNumber($settings->getSubsequentialNumber());
        $dto->setDefaultInvoiceDueTime($settings->getDefaultInvoiceDueTime());
        $dto->setFormat($settings->getInvoiceNumberFormat());
        $dto->setInvoiceGeneration($settings->getInvoiceGenerationType()->value);

        return $dto;
    }
}
