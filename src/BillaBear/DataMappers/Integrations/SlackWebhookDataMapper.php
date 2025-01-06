<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Integrations;

use BillaBear\Dto\Generic\App\Integrations\SlackWebhook as AppDto;
use BillaBear\Dto\Request\App\Integrations\Slack\CreateSlackWebhook;
use BillaBear\Entity\SlackWebhook as Entity;

class SlackWebhookDataMapper
{
    public function buildEntity(CreateSlackWebhook $dto, ?Entity $entity = null): Entity
    {
        if (null === $entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }

        $entity->setName($dto->getName());
        $entity->setWebhookUrl($dto->getWebhook());
        $entity->setEnabled($dto->getEnabled());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setWebhook($entity->getWebhookUrl());
        $dto->setEnabled($entity->isEnabled());

        return $dto;
    }
}
