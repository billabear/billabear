<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Tax;

use BillaBear\DataMappers\CountryDataMapper;
use BillaBear\Dto\Generic\App\State as AppDto;
use BillaBear\Dto\Request\App\Country\CreateState;
use BillaBear\Entity\State as Entity;
use BillaBear\Repository\CountryRepositoryInterface;

class StateDataMapper
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private CountryDataMapper $dataMapper, )
    {
    }

    public function createEntity(CreateState $createState, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
        }

        $entity->setHasNexus($createState->getHasNexus());
        $entity->setName($createState->getName());
        $entity->setCode($createState->getCode());
        $entity->setThreshold($createState->getThreshold());
        $entity->setCountry($this->countryRepository->findById($createState->getCountry()));

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setCode($entity->getCode());
        $dto->setHasNexus($entity->hasNexus());
        $dto->setCountry($this->dataMapper->createAppDto($entity->getCountry()));

        return $dto;
    }
}
