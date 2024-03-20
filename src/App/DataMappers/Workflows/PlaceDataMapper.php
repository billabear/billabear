<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Workflows;

use App\Dto\Generic\App\Workflows\Place as AppDto;
use App\Dto\Request\App\Workflows\CreateTransition;
use App\Entity\WorkflowTransition;
use App\Entity\WorkflowTransition as Entity;
use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class PlaceDataMapper
{
    public function createAppDto(PlaceInterface $place): AppDto
    {
        $dto = new AppDto();
        $dto->setName($place->getName());
        $dto->setPriority($place->getPriority());

        if ($place instanceof WorkflowTransition) {
            $dto->setId((string) $place->getId());
            $dto->setDefault(false);
            $dto->setHandler($place->getHandlerName());
            $dto->setOptions($place->getHandlerOptions());
            $dto->setEnabled($place->isEnabled());
        } else {
            $dto->setDefault(true);
            $dto->setEnabled(true);
        }

        return $dto;
    }

    public function createEntity(CreateTransition $createTransition, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
        }

        $entity->setWorkflow(WorkflowType::fromName($createTransition->getWorkflow()));
        $entity->setName($createTransition->getName());
        $entity->setHandlerName($createTransition->getHandler());
        $entity->setHandlerOptions($createTransition->getHandlerOptions());
        $entity->setPriority($createTransition->getPriority());
        $entity->setEnabled(true);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        return $entity;
    }
}
