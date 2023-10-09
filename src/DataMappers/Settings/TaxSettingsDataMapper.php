<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\Tax\TaxSettings as RequestDto;
use App\Dto\Response\App\Settings\Tax\TaxSettings as AppDto;
use App\Entity\Settings\TaxSettings as Entity;

class TaxSettingsDataMapper
{
    public function createEntity(RequestDto $requestDto): Entity
    {
        $entity = new Entity();
        $entity->setTaxCustomersWithTaxNumbers($requestDto->getTaxCustomersWithTaxNumber());
        $entity->setEuropeanBusinessTaxRules($requestDto->getEuBusinessTaxRules());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setTaxCustomersWithTaxNumber($entity->getTaxCustomersWithTaxNumbers());
        $dto->setEuBusinessTaxRules($entity->getEuropeanBusinessTaxRules());

        return $dto;
    }
}
