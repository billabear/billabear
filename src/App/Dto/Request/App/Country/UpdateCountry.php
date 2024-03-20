<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Country;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCountry
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[SerializedName('iso_code')]
    #[Assert\NotBlank]
    #[Assert\Country]
    private $isoCode;

    #[Assert\NotBlank]
    #[Assert\Currency]
    private $currency;

    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero]
    private $threshold;

    #[SerializedName('in_eu')]
    #[Assert\NotBlank]
    #[Assert\Type('boolean')]
    private $inEu;

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
        return $this->inEu;
    }

    public function setInEu($inEu): void
    {
        $this->inEu = $inEu;
    }
}
