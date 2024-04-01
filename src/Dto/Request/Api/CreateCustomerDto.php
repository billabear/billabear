<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\Api;

use App\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCustomerDto
{
    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('name')]
    private $name;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('brand')]
    private $brand;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[SerializedName('email')]
    private $email;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Locale()]
    #[SerializedName('locale')]
    private $locale;

    #[SerializedName('reference')]
    private $reference;

    #[SerializedName('external_reference')]
    private $externalReference;

    #[SerializedName('billing_type')]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[Assert\Choice(choices: ['invoice', 'card'])]
    private $billingType;

    #[Assert\Valid]
    #[SerializedName('address')]
    private ?Address $address = null;

    #[SerializedName('tax_number')]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    private $taxNumber;

    #[SerializedName('digital_tax_rate')]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type(['integer', 'float'])]
    #[Assert\PositiveOrZero]
    private $digitalTaxRate;

    #[SerializedName('standard_tax_rate')]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type(['integer', 'float'])]
    #[Assert\PositiveOrZero]
    private $standardTaxRate;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[Assert\Choice(choices: ['individual', 'business'])]
    private $type;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
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

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): void
    {
        $this->externalReference = $externalReference;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getBillingType()
    {
        return $this->billingType;
    }

    public function setBillingType($billingType): void
    {
        $this->billingType = $billingType;
    }

    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    public function setTaxNumber($taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    public function getDigitalTaxRate()
    {
        return $this->digitalTaxRate;
    }

    public function setDigitalTaxRate($digitalTaxRate): void
    {
        $this->digitalTaxRate = $digitalTaxRate;
    }

    public function getStandardTaxRate()
    {
        return $this->standardTaxRate;
    }

    public function setStandardTaxRate($standardTaxRate): void
    {
        $this->standardTaxRate = $standardTaxRate;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }
}
