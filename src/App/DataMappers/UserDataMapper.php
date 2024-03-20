<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Request\App\Settings\User\UserUpdate;
use App\Dto\Response\App\Settings\User\User as AppDto;
use App\Entity\User;

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
