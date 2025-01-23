<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

use BillaBear\Validator\Constraints\PriceExists;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePrice
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[PriceExists]
    private $price;

    #[Assert\Choice(['next_cycle', 'instantly'])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $when;

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getWhen()
    {
        return $this->when;
    }

    public function setWhen($when): void
    {
        $this->when = $when;
    }
}
