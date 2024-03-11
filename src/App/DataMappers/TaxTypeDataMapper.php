<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\TaxType as AppDto;
use App\Dto\Request\App\TaxType\CreateTaxType;
use App\Entity\TaxType as Entity;

class TaxTypeDataMapper
{
    public function createEntity(CreateTaxType $createTaxType): Entity
    {
        $entity = new Entity();
        $entity->setName($createTaxType->getName());
        $entity->setPhysical($createTaxType->getPhysical());

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
        $dto->setPhysical($entity->isPhysical());

        return $dto;
    }
}
