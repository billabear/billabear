<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api;

use BillaBear\Dto\Generic\Address;
use BillaBear\Validator\Constraints\Integrations\StripeIsConfigured;
use BillaBear\Validator\Constraints\ValidVatNumber;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[StripeIsConfigured]
class CreateCustomerDto
{
    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('name')]
    private $name;

    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('brand')]
    private $brand;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[SerializedName('email')]
    private $email;

    #[Assert\Locale]
    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('locale')]
    private $locale;

    #[SerializedName('reference')]
    private $reference;

    #[SerializedName('external_reference')]
    private $externalReference;

    #[Assert\Choice(choices: ['invoice', 'card'])]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[SerializedName('billing_type')]
    private $billingType;

    #[Assert\Valid]
    #[SerializedName('address')]
    private ?Address $address = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[SerializedName('tax_number')]
    #[ValidVatNumber]
    private $tax_number;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\PositiveOrZero]
    #[Assert\Type(['integer', 'float'])]
    #[SerializedName('digital_tax_rate')]
    private $digital_tax_rate;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\PositiveOrZero]
    #[Assert\Type(['integer', 'float'])]
    #[SerializedName('standard_tax_rate')]
    private $standard_tax_rate;

    #[Assert\Choice(choices: ['individual', 'business'])]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    private $type;

    #[Assert\Choice(['pdf', 'zugferd_v1'])]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    private $invoice_format;

    #[Assert\Type('boolean')]
    #[SerializedName('marketing_opt_in')]
    private $marketing_opt_in;

    #[Assert\Type('array')]
    private $metadata;

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
        return $this->tax_number;
    }

    public function setTaxNumber($tax_number): void
    {
        $this->tax_number = $tax_number;
    }

    public function getDigitalTaxrate()
    {
        return $this->digital_tax_rate;
    }

    public function setDigitalTaxrate($digital_tax_rate): void
    {
        $this->digital_tax_rate = $digital_tax_rate;
    }

    public function getStandardTaxrate()
    {
        return $this->standard_tax_rate;
    }

    public function setStandardTaxrate($standard_tax_rate): void
    {
        $this->standard_tax_rate = $standard_tax_rate;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getInvoiceFormat()
    {
        return $this->invoice_format;
    }

    public function setInvoiceFormat($invoice_format): void
    {
        $this->invoice_format = $invoice_format;
    }

    public function getMarketingOptin(): bool
    {
        return true === $this->marketing_opt_in;
    }

    public function setMarketingOptin($marketing_opt_in): void
    {
        $this->marketing_opt_in = $marketing_opt_in;
    }

    public function getMetadata(): array
    {
        if (!isset($this->metadata)) {
            return [];
        }

        return $this->metadata;
    }

    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }
}
