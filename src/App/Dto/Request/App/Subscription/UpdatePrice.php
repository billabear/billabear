<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Subscription;

use App\Validator\Constraints\PriceExists;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePrice
{
    #[NotBlank]
    #[PriceExists]
    private $price;

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }
}
