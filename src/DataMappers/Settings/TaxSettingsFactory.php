<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\Tax\TaxSettings as RequestDto;
use App\Dto\Response\App\Settings\Tax\TaxSettings as AppDto;
use App\Entity\Settings\TaxSettings as Entity;

class TaxSettingsFactory
{
    public function createEntity(RequestDto $requestDto): Entity
    {
        $entity = new Entity();
        $entity->setTaxCustomersWithTaxNumbers($requestDto->getTaxCustomersWithTaxNumber());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setTaxCustomersWithTaxNumber($entity->getTaxCustomersWithTaxNumbers());

        return $dto;
    }
}
