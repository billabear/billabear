<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\TaxType as AppDto;
use BillaBear\Dto\Request\App\TaxType\CreateTaxType;
use BillaBear\Entity\TaxType as Entity;

class TaxTypeDataMapper
{
    public function createEntity(CreateTaxType $createTaxType, ?Entity $entity): Entity
    {
        if (null === $entity) {
            $entity = new Entity();
        }
        $entity->setName($createTaxType->getName());
        $entity->setVatSenseType($createTaxType->getVatSenseType());

        return $entity;
    }

    public function createAppDto(?Entity $entity): ?AppDto
    {
        if (!$entity) {
            return null;
        }

        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setDefault($entity->isDefault());
        $dto->setVatSenseType($entity->getVatSenseType());

        return $dto;
    }
}
