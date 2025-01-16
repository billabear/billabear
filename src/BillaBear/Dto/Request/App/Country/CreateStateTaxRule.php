<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use BillaBear\Validator\Constraints\Country\CountryExists;
use BillaBear\Validator\Constraints\State\StateExists;
use BillaBear\Validator\Constraints\StateTaxRule\DoesNotOverlap;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[DoesNotOverlap]
class CreateStateTaxRule
{
    #[Assert\NotBlank]
    #[CountryExists]
    private $country;

    #[Assert\NotBlank]
    #[StateExists]
    private $state;

    #[Assert\NotBlank]
    #[SerializedName('tax_type')]
    private $taxType;

    #[Assert\NotBlank]
    #[Assert\Type(['float', 'integer'])]
    #[SerializedName('tax_rate')]
    private $taxRate;

    #[Assert\DateTime(format: \DATE_RFC3339_EXTENDED)]
    #[Assert\NotBlank]
    #[SerializedName('valid_from')]
    private $validFrom;

    #[Assert\DateTime(format: \DATE_RFC3339_EXTENDED)]
    #[SerializedName('valid_until')]
    private $validUntil;

    private $default;

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

    public function isDefault(): bool
    {
        return true === $this->default;
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

    public function getState()
    {
        return $this->state;
    }

    public function setState($state): void
    {
        $this->state = $state;
    }
}
