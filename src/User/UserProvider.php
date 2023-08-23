<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
