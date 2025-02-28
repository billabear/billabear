<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Entity\BrandSettings\NotificationSettings;
use Doctrine\ORM\Mapping as ORM;
use Parthenon\Common\Address;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Index(name: 'brand_code_idx', columns: ['code'])]
#[ORM\Table(name: 'brand_settings')]
class BrandSettings
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $taxNumber = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taxRate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $digitalServicesRate = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $supportEmail = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $supportPhoneNumber = null;

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

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function hasTaxRate(): bool
    {
        return isset($this->taxRate);
    }

    public function getDigitalServicesRate(): ?float
    {
        return $this->digitalServicesRate;
    }

    public function setDigitalServicesRate(?float $digitalServicesRate): void
    {
        $this->digitalServicesRate = $digitalServicesRate;
    }

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(?string $supportEmail): void
    {
        $this->supportEmail = $supportEmail;
    }

    public function getSupportPhoneNumber(): ?string
    {
        return $this->supportPhoneNumber;
    }

    public function setSupportPhoneNumber(?string $supportPhoneNumber): void
    {
        $this->supportPhoneNumber = $supportPhoneNumber;
    }
}
