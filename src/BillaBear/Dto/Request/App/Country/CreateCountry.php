<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use BillaBear\Validator\Constraints\Country\UniqueCountryCode;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCountry
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\Country]
    #[Assert\NotBlank]
    #[SerializedName('iso_code')]
    #[UniqueCountryCode]
    private $isoCode;

    #[Assert\Currency]
    #[Assert\NotBlank]
    private $currency;

    #[Assert\PositiveOrZero]
    #[Assert\Type('integer')]
    private $threshold;

    #[Assert\Type('boolean')]
    private $default;

    #[Assert\Type('boolean')]
    #[SerializedName('in_eu')]
    private $inEu = false;

    #[SerializedName('start_of_tax_year')]
    private $startOfTaxYear;

    #[Assert\Type('boolean')]
    private $enabled = true;

    #[Assert\Type('boolean')]
    private $collecting = false;

    #[Assert\Type('string')]
    private $taxNumber;

    #[Assert\GreaterThan(0)]
    #[Assert\Type('integer')]
    #[SerializedName('transaction_threshold')]
    private $transactionThreshold;

    #[Assert\Choice(choices: ['rolling', 'calendar', 'rolling_quarterly', 'rolling_accounting'])]
    #[Assert\Type('string')]
    #[SerializedName('threshold_type')]
    private $thresholdType;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getIsoCode()
    {
        return $this->isoCode;
    }

    public function setIsoCode($isoCode): void
    {
        $this->isoCode = $isoCode;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function setThreshold($threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getInEu()
    {
        return true === $this->inEu;
    }

    public function setInEu($inEu): void
    {
        $this->inEu = $inEu;
    }

    public function getDefault()
    {
        return true === $this->default;
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }

    public function getStartOfTaxYear()
    {
        return $this->startOfTaxYear;
    }

    public function setStartOfTaxYear($startOfTaxYear): void
    {
        $this->startOfTaxYear = $startOfTaxYear;
    }

    public function isEnabled(): bool
    {
        return true === $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCollecting()
    {
        return true === $this->collecting;
    }

    public function setCollecting(bool $collecting): void
    {
        $this->collecting = $collecting;
    }

    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    public function setTaxNumber($taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }
}
