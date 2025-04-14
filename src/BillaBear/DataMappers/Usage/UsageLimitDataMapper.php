<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Usage;

use BillaBear\Dto\Generic\Api\Usage\UsageLimit as ApiDto;
use BillaBear\Dto\Generic\App\Usage\UsageLimit as AppDto;
use BillaBear\Dto\Request\Api\Usage\CreateUsageLimit as ApiCreate;
use BillaBear\Dto\Request\App\Usage\CreateUsageLimit as AppCreate;
use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit as Entity;
use BillaBear\Pricing\Usage\WarningLevel;

class UsageLimitDataMapper
{
    public function createEntityFromApp(Customer $customer, AppCreate $createUsageLimit): Entity
    {
        $entity = new Entity();
        $entity->setCustomer($customer);
        $entity->setAmount($createUsageLimit->getAmount());
        $entity->setWarningLevel(WarningLevel::from($createUsageLimit->getWarnLevel()));

        return $entity;
    }

    public function createEntityFromApi(Customer $customer, ApiCreate $createUsageLimit): Entity
    {
        $entity = new Entity();
        $entity->setCustomer($customer);
        $entity->setAmount($createUsageLimit->getAmount());
        $entity->setWarningLevel(WarningLevel::fromName($createUsageLimit->getAction()));

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        return new AppDto(
            (string) $entity->getId(),
            $entity->getAmount(),
            $entity->getWarningLevel(),
        );
    }

    public function createApiDto(Entity $entity): ApiDto
    {
        $dto = new ApiDto(
            (string) $entity->getId(),
            $entity->getAmount(),
            $entity->getWarningLevel()->name,
        );

        return $dto;
    }
}
