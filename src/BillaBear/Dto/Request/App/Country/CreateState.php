<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Country;

use BillaBear\Validator\Constraints\Country\CountryExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateState
{
    #[CountryExists]
    private $country;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    private $name;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    private $code;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\Type('integer')]
    private $threshold;

    #[Assert\Type('boolean')]
    private $hasNexus;

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

    public function getHasNexus()
    {
        return true === $this->hasNexus;
    }

    public function setHasNexus($hasNexus): void
    {
        $this->hasNexus = $hasNexus;
    }
}
