<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
