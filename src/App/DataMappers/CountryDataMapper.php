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

use App\Dto\Generic\App\Country as AppDto;
use App\Dto\Request\App\Country\CreateCountry;
use App\Dto\Request\App\Country\UpdateCountry;
use App\Entity\Country as Entity;

class CountryDataMapper
{
    public function createEntity(CreateCountry|UpdateCountry $updateCountry, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime('now'));
            $entity->setEnabled(true);
        }

        $entity->setName($updateCountry->getName());
        $entity->setIsoCode($updateCountry->getIsoCode());
        $entity->setCurrency($updateCountry->getCurrency());
        $entity->setThreshold($updateCountry->getThreshold());
        $entity->setInEu($updateCountry->getInEu() ?? false);

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setId($entity->getId());
        $appDto->setName($entity->getName());
        $appDto->setIsoCode($entity->getIsoCode());
        $appDto->setCurrency($entity->getCurrency());
        $appDto->setThreshold($entity->getThreshold());
        $appDto->setInEu($entity->isInEu());

        return $appDto;
    }
}
