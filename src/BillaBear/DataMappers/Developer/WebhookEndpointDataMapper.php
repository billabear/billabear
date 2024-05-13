<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Developer;

use BillaBear\Dto\Request\App\Developer\Webhook\CreateWebhookEndpoint;
use BillaBear\Dto\Response\App\Developer\Webhook\WebhookEndpoint as AppDto;
use BillaBear\Entity\WebhookEndpoint as Entity;

class WebhookEndpointDataMapper
{
    public function createEntity(CreateWebhookEndpoint $createWebhookEndpoint, ?Entity $entity = null): Entity
    {
        if (!$entity instanceof Entity) {
            $entity = new Entity();
            $entity->setActive(true);
            $entity->setCreatedAt(new \DateTime());
        }
        $entity->setUrl($createWebhookEndpoint->getUrl());
        $entity->setName($createWebhookEndpoint->getName());
        $entity->setUpdatedAt(new \DateTime());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setUrl($entity->getUrl());
        $dto->setActive($entity->isActive());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }
}
