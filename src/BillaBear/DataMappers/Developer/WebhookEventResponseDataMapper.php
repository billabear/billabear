<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Developer;

use BillaBear\Dto\Response\App\Developer\Webhook\WebhookEventResponse as AppDto;
use BillaBear\Entity\WebhookEventResponse as Entity;

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
