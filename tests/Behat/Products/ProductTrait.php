<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Products;

use BillaBear\Entity\Product;

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
