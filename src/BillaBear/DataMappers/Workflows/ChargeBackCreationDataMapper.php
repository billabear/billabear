<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Workflows;

use BillaBear\DataMappers\ChargeBackDataMapper;
use BillaBear\Dto\Generic\App\Workflows\ChargeBackCreation as AppDto;
use BillaBear\Entity\ChargeBackCreation as Entity;

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
