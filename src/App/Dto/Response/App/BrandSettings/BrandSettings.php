<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\BrandSettings;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

class BrandSettings
{
    private string $id;

    private string $code;

    private string $name;

    #[SerializedName('email_address')]
    private string $emailAddress;

    private Address $address;

    #[SerializedName('is_default')]
    private bool $isDefault;

    private Notifications $notifications;

    #[SerializedName('tax_number')]
    private ?string $taxNumber;

    #[SerializedName('tax_rate')]
    private ?float $taxRate;

    #[SerializedName('digital_services_tax_rate')]
    private ?float $digitalServicesTaxRate;

    public function __construct()
    {
        $this->notifications = new Notifications();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getNotifications(): Notifications
    {
        return $this->notifications;
    }

    public function setNotifications(Notifications $notifications): void
    {
        $this->notifications = $notifications;
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

    public function getDigitalServicesTaxRate(): ?float
    {
        return $this->digitalServicesTaxRate;
    }

    public function setDigitalServicesTaxRate(?float $digitalServicesTaxRate): void
    {
        $this->digitalServicesTaxRate = $digitalServicesTaxRate;
    }
}
