<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Developer;

use App\Dto\Response\App\Developer\Webhook\WebhookEventResponse as AppDto;
use App\Entity\WebhookEventResponse as Entity;

class WebhookEventResponseDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setUrl($entity->getUrl());
        $dto->setStatusCode($entity->getStatusCode());
        $dto->setBody($entity->getBody());
        $dto->setErrorMessage($entity->getErrorMessage());
        $dto->setProcessingTime($entity->getProcessingTime());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }
}
