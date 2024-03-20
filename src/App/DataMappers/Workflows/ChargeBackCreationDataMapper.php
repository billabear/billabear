<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Workflows;

use App\DataMappers\ChargeBackDataMapper;
use App\Dto\Generic\App\Workflows\ChargeBackCreation as AppDto;
use App\Entity\ChargeBackCreation as Entity;

class ChargeBackCreationDataMapper
{
    public function __construct(private ChargeBackDataMapper $chargeBackDataMapper)
    {
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();

        $dto->setId((string) $entity->getId());
        $dto->setState($entity->getState());
        $dto->setError($entity->getError());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setChargeBack($this->chargeBackDataMapper->createAppDto($entity->getChargeBack()));

        return $dto;
    }
}
