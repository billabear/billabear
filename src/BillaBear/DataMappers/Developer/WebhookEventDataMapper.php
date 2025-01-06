<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Developer;

use BillaBear\Dto\Response\App\Developer\Webhook\WebhookEvent as AppDto;
use BillaBear\Entity\WebhookEvent as Entity;

class WebhookEventDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setType($entity->getType()->name);
        $dto->setPayload($entity->getPayload());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }
}
