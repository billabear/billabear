<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
