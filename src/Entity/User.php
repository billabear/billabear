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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\BillingAdminInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User extends \Parthenon\User\Entity\User implements BillingAdminInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_DEVELOPER = 'ROLE_DEVELOPER';
    public const ROLE_CUSTOMER_SUPPORT = 'ROLE_CUSTOMER_SUPPORT';
    public const ROLE_USER = 'ROLE_USER';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
        self::ROLE_CUSTOMER_SUPPORT,
        self::ROLE_USER,
    ];

    public function getDisplayName(): string
    {
        return $this->email;
    }
}
