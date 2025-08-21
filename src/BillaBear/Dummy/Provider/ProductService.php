<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Provider;

use Obol\Model\Product;
use Obol\Model\ProductCreation;
use Obol\ProductServiceInterface;

class ProductService implements ProductServiceInterface
{
    public function createProduct(Product $product): ProductCreation
    {
        $productCreation = new ProductCreation();
        $productCreation->setReference(bin2hex(random_bytes(32)));

        return $productCreation;
    }

    public function fetchProduct(string $productId): Product
    {
        // TODO: Implement fetchProduct() method.
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}
