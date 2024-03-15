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
