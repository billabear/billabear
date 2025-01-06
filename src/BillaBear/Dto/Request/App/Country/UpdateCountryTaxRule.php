<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use BillaBear\Validator\Constraints\Country\CountryExists;
use BillaBear\Validator\Constraints\CountryTaxRule\DoesNotOverlap;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[DoesNotOverlap]
class UpdateCountryTaxRule
{
    #[Assert\NotBlank]
    private $id;

    #[Assert\NotBlank]
    #[CountryExists]
    private $country;

    #[Assert\NotBlank]
    #[SerializedName('tax_type')]
    private $taxType;

    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    #[SerializedName('tax_rate')]
    private $taxRate;

    #[Assert\Type('boolean')]
    private $default = false;

    #[Assert\AtLeastOneOf([new Assert\DateTime(format: \DATE_RFC3339_EXTENDED), new Assert\DateTime(format: \DATE_ATOM)])]
    #[Assert\NotBlank]
    #[SerializedName('valid_from')]
    private $validFrom;

    #[Assert\AtLeastOneOf([new Assert\DateTime(format: \DATE_RFC3339_EXTENDED), new Assert\DateTime(format: \DATE_ATOM)])]
    #[SerializedName('valid_until')]
    private $validUntil;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }

    public function getTaxType()
    {
        return $this->taxType;
    }

    public function setTaxType($taxType): void
    {
        $this->taxType = $taxType;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    public function setTaxRate($taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }

    public function getValidFrom()
    {
        return $this->validFrom;
    }

    public function setValidFrom($validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidUntil()
    {
        return $this->validUntil;
    }

    public function setValidUntil($validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function isInEu(): bool
    {
        return $this->inEu;
    }

    public function setInEu(bool $inEu): void
    {
        $this->inEu = $inEu;
    }
}
