<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers\Settings;

use App\Dto\Response\App\Settings\StripeImport as AppDto;
use App\Entity\StripeImport as Entity;

class StripeImportDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setState($entity->getState());
        $dto->setLastId($entity->getLastId());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setUpdateAt($entity->getUpdatedAt());
        $dto->setError($entity->getError());

        return $dto;
    }
}
