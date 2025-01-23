<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Request\App\Settings\User\UserUpdate;
use BillaBear\Dto\Response\App\Settings\User\User as AppDto;
use BillaBear\Entity\User;

class UserDataMapper
{
    public function createAppDto(User $user): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $user->getId());
        $dto->setEmail($user->getEmail());
        $dto->setRoles($user->getRoles());

        return $dto;
    }

    public function updateEntity(User $user, UserUpdate $userUpdate): User
    {
        $user->setEmail($userUpdate->getEmail());
        $user->setRoles($userUpdate->getRoles());

        return $user;
    }
}
