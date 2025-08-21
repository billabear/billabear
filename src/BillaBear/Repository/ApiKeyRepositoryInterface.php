<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\ApiKey;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;

interface ApiKeyRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ApiKey[]
     */
    public function getAll(): array;

    public function hasApiKeyForName(string $name): bool;

    /**
     * @throws NoEntityFoundException
     */
    public function findActiveApiKeyForKey(string $key): ApiKey;
}
