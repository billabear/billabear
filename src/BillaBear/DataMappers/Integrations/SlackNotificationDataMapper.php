<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Integrations;

use BillaBear\Dto\Generic\App\Integrations\SlackNotification as AppDto;
use BillaBear\Dto\Request\App\Integrations\Slack\CreateSlackNotification;
use BillaBear\Entity\SlackNotification as Entity;
use BillaBear\Notification\Slack\SlackNotificationEvent;
use BillaBear\Repository\SlackWebhookRepositoryInterface;

class SlackNotificationDataMapper
{
    public function __construct(
        private SlackWebhookRepositoryInterface $slackWebhookRepository,
        private SlackWebhookDataMapper $slackWebhookDataMapper,
    ) {
    }

    public function createEntity(CreateSlackNotification $dto, ?Entity $entity = null): Entity
    {
        if (!$entity) {
            $entity = new Entity();
            $entity->setCreatedAt(new \DateTime());
        }
        $entity->setSlackWebhook($this->slackWebhookRepository->getById($dto->getWebhook()));
        $event = SlackNotificationEvent::from($dto->getEvent());
        $entity->setEvent($event);
        $entity->setMessageTemplate($dto->getTemplate());

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        return new AppDto(
            (string) $entity->getId(),
            $this->slackWebhookDataMapper->createAppDto($entity->getSlackWebhook()),
            $entity->getEvent(),
            $entity->getMessageTemplate()
        );
    }
}
