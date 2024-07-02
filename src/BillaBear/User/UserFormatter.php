<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
