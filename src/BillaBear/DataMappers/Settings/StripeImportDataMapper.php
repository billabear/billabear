<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Settings;

use BillaBear\Dto\Response\App\Settings\StripeImport as AppDto;
use BillaBear\Entity\StripeImport as Entity;

class StripeImportDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        return new AppDto(
            (string) $entity->getId(),
            $entity->getState(),
            $entity->getLastId(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
            $entity->getError(),
            $entity->isComplete(),
            $entity->hasFailed(),
        );
    }
}
