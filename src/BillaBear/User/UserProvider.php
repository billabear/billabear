<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\User;

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
