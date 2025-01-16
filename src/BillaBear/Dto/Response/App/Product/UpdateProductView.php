<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Product;

use BillaBear\Dto\Generic\App\Product;
use Symfony\Component\Serializer\Attribute\SerializedName;

class UpdateProductView
{
    private Product $product;

    #[SerializedName('tax_types')]
    private array $taxTypes = [];

    public function getTaxTypes(): array
    {
        return $this->taxTypes;
    }

    public function setTaxTypes(array $taxTypes): void
    {
        $this->taxTypes = $taxTypes;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
