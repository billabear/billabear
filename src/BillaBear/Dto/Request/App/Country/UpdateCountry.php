<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCountry
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\Country]
    #[Assert\NotBlank]
    #[SerializedName('iso_code')]
    private $isoCode;

    #[Assert\Currency]
    #[Assert\NotBlank]
    private $currency;

    #[Assert\PositiveOrZero]
    #[Assert\Type('integer')]
    private $threshold;

    #[SerializedName('in_eu')]
    private $inEu;

    #[SerializedName('tax_year')]
    private $taxYear;

    #[SerializedName('start_of_tax_year')]
    private $startOfTaxYear;

    #[Assert\Type('boolean')]
    private $enabled;

    #[Assert\Type('boolean')]
    private $collecting;

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

    public function getTaxYear()
    {
        return $this->taxYear;
    }

    public function setTaxYear($taxYear): void
    {
        $this->taxYear = $taxYear;
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

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCollecting()
    {
        return true === $this->collecting;
    }

    public function setCollecting($collecting): void
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

    public function getTransactionThreshold()
    {
        return $this->transactionThreshold;
    }

    public function setTransactionThreshold($transactionThreshold): void
    {
        $this->transactionThreshold = $transactionThreshold;
    }

    public function getThresholdType()
    {
        return $this->thresholdType;
    }

    public function setThresholdType($thresholdType): void
    {
        $this->thresholdType = $thresholdType;
    }
}
