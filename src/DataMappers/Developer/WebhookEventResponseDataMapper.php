<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
