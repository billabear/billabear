<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
