<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\User;

use BillaBear\Entity\User as BillaBearUser;
use Parthenon\User\Entity\User;
use Parthenon\User\Formatter\UserFormatterInterface;

class UserFormatter implements UserFormatterInterface
{
    /**
     * @param BillaBearUser $user
     */
    public function format(User $user): array
    {
        return [
            'id' => (string) $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'locale' => $user->getLocale(),
        ];
    }
}
