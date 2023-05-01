<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory;

use App\Dto\Request\App\Settings\User\UserUpdate;
use App\Dto\Response\App\Settings\User\User as AppDto;
use App\Entity\User;

class UserFactory
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
