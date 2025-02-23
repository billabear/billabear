<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Request\App\Settings\Tax\TaxSettings as RequestDto;
use BillaBear\Dto\Request\App\Settings\Tax\VatSense;
use BillaBear\Dto\Response\App\Settings\Tax\TaxSettings as AppDto;
use BillaBear\Entity\Settings\TaxSettings as Entity;

class TaxSettingsDataMapper
{
    public function updateTaxSettings(RequestDto $requestDto, Entity $entity): Entity
    {
        $entity->setTaxCustomersWithTaxNumbers($requestDto->getTaxCustomersWithTaxNumber());
        $entity->setEuropeanBusinessTaxRules($requestDto->getEuBusinessTaxRules());
        $entity->setOneStopShopTaxRules($requestDto->getEuOneStopShopRule());

        return $entity;
    }

    public function updateVatSense(VatSense $vatSense, Entity $entity): Entity
    {
        $entity->setValidateTaxNumber($vatSense->getValidateVatIds());
        $entity->setVatSenseEnabled($vatSense->getVatSenseEnabled());
        $entity->setVatSenseApiKey($vatSense->getVatSenseApiKey());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setTaxCustomersWithTaxNumber($entity->getTaxCustomersWithTaxNumbers());
        $dto->setEuBusinessTaxRules($entity->getEuropeanBusinessTaxRules());
        $dto->setEuOneStopShopRule($entity->getOneStopShopTaxRules());
        $dto->setVatSenseEnabled($entity->getVatSenseEnabled());
        $dto->setVatSenseApiKey($entity->getVatSenseApiKey());
        $dto->setValidateVatIds($entity->getValidateTaxNumber());

        return $dto;
    }
}
