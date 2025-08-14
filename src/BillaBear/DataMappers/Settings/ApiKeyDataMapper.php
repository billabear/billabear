<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Request\App\Settings\CreateApiKey;
use BillaBear\Dto\Response\App\Settings\ApiKey as AppDto;
use BillaBear\Entity\ApiKey as Entity;

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
        $apiKey->setName($createApiKey->name);
        $apiKey->setExpiresAt(\DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createApiKey->expiresAt));

        return $apiKey;
    }
}
