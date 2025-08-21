<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
        return new AppDto(
            (string) $entity->getId(),
            $entity->getName(),
            $entity->getWebhookUrl(),
            $entity->isEnabled()
        );
    }
}
