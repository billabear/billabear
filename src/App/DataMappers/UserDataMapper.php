<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
