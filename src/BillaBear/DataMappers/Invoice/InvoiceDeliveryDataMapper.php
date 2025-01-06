<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Invoice;

use BillaBear\Dto\Generic\App\InvoiceDelivery as AppDto;
use BillaBear\Entity\InvoiceDelivery as Entity;

class InvoiceDeliveryDataMapper
{
    public function __construct(private InvoiceDeliverySettingsDataMapper $settingsDataMapper)
    {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setId((string) $entity->getId());
        $appDto->setInvoiceDeliverySettings($this->settingsDataMapper->createAppDto($entity->getInvoiceDeliverySettings()));
        $appDto->setStatus($entity->getStatus()->value);
        $appDto->setCreatedAt($entity->getCreatedAt());

        return $appDto;
    }
}
