<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Generic\App\InviteCode as AppDto;
use Parthenon\User\Entity\InviteCode as Entity;

class InviteCodeDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setEmail($entity->getEmail());
        $appDto->setSentAt($entity->getCreatedAt());
        $appDto->setCode($entity->getCode());
        $appDto->setRole($entity->getRole());

        return $appDto;
    }
}
