<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\App\InviteCode as AppDto;
use Parthenon\User\Entity\InviteCode as Entity;

class InviteCodeDataMapper
{
    public function createAppDto(Entity $entity): AppDto
    {
        $appDto = new AppDto();
        $appDto->setEmail($entity->getEmail());
        $appDto->setSentAt($entity->getCreatedAt());
        $appDto->setCode($entity->getCode());

        return $appDto;
    }
}
