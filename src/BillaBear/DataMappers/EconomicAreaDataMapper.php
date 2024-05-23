<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\EconomicArea as AppDto;
use BillaBear\Dto\Request\App\EconomicArea\CreateEconomicArea;
use BillaBear\Entity\EconomicArea as Entity;

class EconomicAreaDataMapper
{
    public function __construct(private EconomicAreaMembershipDatamapper $economicAreaMembershipDatamapper)
    {
    }

    public function createEntity(CreateEconomicArea $createEconomicArea, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }
        $entity->setName($createEconomicArea->getName());
        $entity->setThreshold($createEconomicArea->getThreshold());
        $entity->setCurrency($createEconomicArea->getCurrency());
        $entity->setEnabled($createEconomicArea->isEnabled());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId($entity->getId());
        $dto->setName($entity->getName());
        $dto->setThreshold($entity->getThreshold());
        $dto->setCurrency($entity->getCurrency());
        $dto->setCreatedAt($entity->getCreatedAt());

        $members = [];
        foreach ($entity->getMembers() as $member) {
            $members[] = $this->economicAreaMembershipDatamapper->createAppDto($member);
        }
        $dto->setMembers($members);

        return $dto;
    }
}
