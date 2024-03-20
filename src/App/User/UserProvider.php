<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\User;

use Parthenon\Billing\Entity\BillingAdminInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserProvider
{
    public function __construct(private Security $security)
    {
    }

    public function getUser(): BillingAdminInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof BillingAdminInterface) {
            throw new \Exception('Invalid user type got '.get_class($user));
        }

        return $user;
    }
}
