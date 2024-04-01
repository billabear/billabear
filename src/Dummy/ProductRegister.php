<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy;

use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\PriceRegisterInterface;
use Parthenon\Billing\Obol\ProductRegisterInterface;

class ProductRegister implements ProductRegisterInterface, PriceRegisterInterface
{
    public function registerProduct(Product $product): Product
    {
        return $product;
    }

    public function registerPrice(Price $price): Price
    {
        return $price;
    }
}
