<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

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

    public const DEFAULT_LOCALE = 'en';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
        self::ROLE_CUSTOMER_SUPPORT,
        self::ROLE_USER,
    ];
    #[ORM\Column(name: 'locale', type: 'string', length: 255, nullable: true)]
    private ?string $locale = self::DEFAULT_LOCALE;

    public function getDisplayName(): string
    {
        return $this->email;
    }

    public function getLocale(): string
    {
        return $this->locale ?? self::DEFAULT_LOCALE;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
