<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Products;

use App\Entity\Product;

trait ProductTrait
{
    public function getProductByName(string $name): Product
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if (!$product instanceof Product) {
            throw new \Exception('No product found');
        }

        $this->productRepository->getEntityManager()->refresh($product);

        return $product;
    }
}
