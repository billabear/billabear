<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Developer;

use App\Dto\Response\App\Developer\Webhook\WebhookEvent as AppDto;
use App\Entity\WebhookEvent as Entity;

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
