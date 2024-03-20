<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\ApiKey;
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
