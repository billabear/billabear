<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use BillaBear\Validator\Constraints\Country\CountryExists;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateState
{
    #[CountryExists]
    private $country;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $code;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Type('integer')]
    private $threshold;

    #[Assert\Type('boolean')]
    private $collecting;

    #[Assert\GreaterThan(0)]
    #[Assert\Type('integer')]
    #[SerializedName('transaction_threshold')]
    private $transactionThreshold;

    #[Assert\Choice(choices: ['rolling', 'calendar', 'rolling_quarterly', 'rolling_accounting'])]
    #[Assert\Type('string')]
    #[SerializedName('threshold_type')]
    private $thresholdType;

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function setThreshold($threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getCollecting()
    {
        return true === $this->collecting;
    }

    public function setCollecting($collecting): void
    {
        $this->collecting = $collecting;
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
