<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Generic\Api;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Customer
{
    #[SerializedName('id')]
    protected string $id;

    #[SerializedName('name')]
    protected ?string $name = null;

    #[SerializedName('email')]
    protected string $email;

    #[SerializedName('reference')]
    protected ?string $reference = null;

    #[SerializedName('external_reference')]
    protected string $externalReference;

    #[SerializedName('address')]
    protected Address $address;

    #[SerializedName('status')]
    protected string $status;

    #[SerializedName('brand')]
    protected string $brand;

    #[SerializedName('locale')]
    protected string $locale;

    #[SerializedName('billing_type')]
    protected string $billingType;

    #[SerializedName('tax_number')]
    protected ?string $taxNumber;

    #[SerializedName('digital_tax_rate')]
    protected ?float $digitalTaxRate;

    #[SerializedName('standard_tax_rate')]
    protected ?float $standardTaxRate;

    protected string $type;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getExternalReference(): string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getBillingType(): string
    {
        return $this->billingType;
    }

    public function setBillingType(string $billingType): void
    {
        $this->billingType = $billingType;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getDigitalTaxRate(): ?float
    {
        return $this->digitalTaxRate;
    }

    public function setDigitalTaxRate(?float $digitalTaxRate): void
    {
        $this->digitalTaxRate = $digitalTaxRate;
    }

    public function getStandardTaxRate(): ?float
    {
        return $this->standardTaxRate;
    }

    public function setStandardTaxRate(?float $standardTaxRate): void
    {
        $this->standardTaxRate = $standardTaxRate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
