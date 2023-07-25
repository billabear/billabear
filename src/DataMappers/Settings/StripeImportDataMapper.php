<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
