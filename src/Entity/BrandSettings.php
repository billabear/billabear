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

use App\Entity\BrandSettings\NotificationSettings;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'brand_settings')]
#[ORM\Index(name: 'code_idx', columns: ['code'])]
class BrandSettings
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $brandName;

    #[ORM\Column(type: 'string')]
    private string $emailAddress;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[ORM\Column(type: 'boolean')]
    private bool $isDefault = false;

    #[ORM\Embedded(class: NotificationSettings::class)]
    private NotificationSettings $notificationSettings;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getBrandName(): string
    {
        return $this->brandName;
    }

    public function setBrandName(string $brandName): void
    {
        $this->brandName = $brandName;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getNotificationSettings(): NotificationSettings
    {
        if (!isset($this->notificationSettings)) {
            $this->notificationSettings = new NotificationSettings();
        }

        return $this->notificationSettings;
    }

    public function setNotificationSettings(NotificationSettings $notificationSettings): void
    {
        $this->notificationSettings = $notificationSettings;
    }
}
