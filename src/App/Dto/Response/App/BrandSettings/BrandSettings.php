<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
