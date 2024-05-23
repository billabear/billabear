<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\EconomicArea;

use BillaBear\Validator\Constraints\EconomicArea\UniqueEconomicArea;
use Symfony\Component\Validator\Constraints as Assert;

class CreateEconomicArea
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[UniqueEconomicArea]
    private $name;

    #[Assert\NotBlank()]
    #[Assert\Type('integer')]
    #[Assert\GreaterThanOrEqual(0)]
    private $threshold;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[Assert\Currency()]
    private $currency;

    private $enabled;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function setThreshold($threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    public function isEnabled(): bool
    {
        return true === $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }
}
