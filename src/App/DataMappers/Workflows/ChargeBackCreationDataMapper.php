<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
