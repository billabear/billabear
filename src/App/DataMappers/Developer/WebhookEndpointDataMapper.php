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

namespace App\DataMappers\Developer;

use App\Dto\Request\App\Developer\Webhook\CreateWebhookEndpoint;
use App\Dto\Response\App\Developer\Webhook\WebhookEndpoint as AppDto;
use App\Entity\WebhookEndpoint as Entity;

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
