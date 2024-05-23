<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\EconomicAreaMembership as AppDto;
use BillaBear\Dto\Request\App\EconomicArea\CreateMembership;
use BillaBear\Dto\Request\App\EconomicArea\UpdateMembership;
use BillaBear\Entity\EconomicAreaMembership as Entity;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\EconomicAreaRepositoryInterface;

class EconomicAreaMembershipDatamapper
{
    public function __construct(
        private EconomicAreaRepositoryInterface $economicAreaRepository,
        private CountryDataMapper $dataMapper,
        private CountryRepositoryInterface $countryRepository, private readonly CountryDataMapper $countryDataMapper,
    ) {
    }

    public function createEntity(CreateMembership $createMembership, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }

        $economicArea = $this->economicAreaRepository->getById($createMembership->getEconomicArea());
        $country = $this->countryRepository->getById($createMembership->getCountry());

        $joinedAt = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $createMembership->getJoinedAt());

        $entity->setEconomicArea($economicArea);
        $entity->setJoinedAt($joinedAt);
        $entity->setCountry($country);

        if ($createMembership->getLeftAt()) {
            $leftAt = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $createMembership->getLeftAt());
            $entity->setLeftAt($leftAt);
        }

        return $entity;
    }

    public function updateEntity(UpdateMembership $updateMembership, Entity $entity): Entity
    {
        $joinedAt = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $updateMembership->getJoinedAt());
        $entity->setJoinedAt($joinedAt);
        if ($updateMembership->getLeftAt()) {
            $leftAt = \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, $updateMembership->getLeftAt());
            $entity->setLeftAt($leftAt);
        }

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setCountry($this->countryDataMapper->createAppDto($entity->getCountry()));
        $dto->setJoinedAt($entity->getJoinedAt());
        $dto->setLeftAt($entity->getLeftAt());
        $dto->setId((string) $entity->getId());

        return $dto;
    }
}
