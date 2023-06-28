<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dummy\Provider;

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

    public function list(int $limit = 10, string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}
