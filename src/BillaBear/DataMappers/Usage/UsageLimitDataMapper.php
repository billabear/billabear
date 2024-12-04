<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Generic\Api\Usage\UsageLimit as ApiDto;
use BillaBear\Dto\Generic\App\Usage\UsageLimit as AppDto;
use BillaBear\Dto\Request\App\Usage\CreateUsageLimit;
use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit as Entity;
use BillaBear\Enum\WarningLevel;

class UsageLimitDataMapper
{
    public function createEntity(Customer $customer, CreateUsageLimit $createUsageLimit): Entity
    {
        $entity = new Entity();
        $entity->setCustomer($customer);
        $entity->setAmount($createUsageLimit->getAmount());
        $entity->setWarningLevel(WarningLevel::from($createUsageLimit->getWarnLevel()));

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setAmount($entity->getAmount());
        $dto->setWarnLevel($entity->getWarningLevel()->value);

        return $dto;
    }

    public function createApiDto(Entity $entity): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $entity->getId());
        $dto->setAmount($entity->getAmount());
        $dto->setAction($entity->getWarningLevel()->name);

        return $dto;
    }
}
