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

namespace App\DataMappers\Settings;

use App\Dto\Request\App\Settings\CreateApiKey;
use App\Dto\Response\App\Settings\ApiKey as AppDto;
use App\Entity\ApiKey as Entity;

class ApiKeyDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setKey($entity->getKey());
        $dto->setName($entity->getName());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setExpiresAt($entity->getExpiresAt());
        $dto->setActive($entity->isActive());

        return $dto;
    }

    public function createEntity(CreateApiKey $createApiKey): Entity
    {
        $apiKey = new Entity();
        $apiKey->setKey(bin2hex(random_bytes(24)));
        $apiKey->setCreatedAt(new \DateTime());
        $apiKey->setUpdatedAt(new \DateTime());
        $apiKey->setName($createApiKey->getName());
        $apiKey->setExpiresAt(\DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createApiKey->getExpiresAt()));

        return $apiKey;
    }
}
