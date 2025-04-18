<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\ApiKey;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class ApiKeyRepository extends DoctrineRepository implements ApiKeyRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function hasApiKeyForName(string $name): bool
    {
        $apiKey = $this->entityRepository->findOneBy(['name' => $name]);

        return $apiKey instanceof ApiKey;
    }

    public function findActiveApiKeyForKey(string $key): ApiKey
    {
        $apiKey = $this->entityRepository->findOneBy(['key' => $key, 'active' => true]);

        if (!$apiKey instanceof ApiKey) {
            throw new NoEntityFoundException(sprintf("Unable to find api key for key '%s'", $key));
        }

        return $apiKey;
    }
}
