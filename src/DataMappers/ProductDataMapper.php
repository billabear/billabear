<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\Product as ProductDto;
use App\Dto\Generic\App\Product as AppDto;
use App\Dto\Request\Api\CreateProduct as ApiCreate;
use App\Dto\Request\App\CreateProduct as AppCreate;
use App\Entity\Product;
use App\Enum\TaxType;

class ProductDataMapper
{
    public function createFromApiCreate(ApiCreate $createProduct, Product $product = null): Product
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($createProduct->getName());
        $product->setExternalReference($createProduct->getExternalReference());
        $product->setTaxType(TaxType::fromName($createProduct->getTaxType()));

        return $product;
    }

    public function createFromAppCreate(AppCreate $createProduct, Product $product = null): Product
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($createProduct->getName());
        $product->setExternalReference($createProduct->getExternalReference());
        $product->setTaxType(TaxType::fromName($createProduct->getTaxType()));
        if ($createProduct->getTaxRate()) {
            $product->setTaxRate(floatval($createProduct->getTaxRate()));
        }

        return $product;
    }

    public function createApiDtoFromProduct(Product $product): ProductDto
    {
        $dto = new ProductDto();
        $dto->setId((string) $product->getId());
        $dto->setName($product->getName());
        $dto->setExternalReference($product->getExternalReference());
        $dto->setTaxType($product->getTaxType()->value);

        return $dto;
    }

    public function createAppDtoFromProduct(Product $product): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $product->getId());
        $dto->setName($product->getName());
        $dto->setExternalReference($product->getExternalReference());
        $dto->setPaymentProviderDetailsUrl($product->getPaymentProviderDetailsUrl());
        $dto->setTaxType($product->getTaxType()->value);
        $dto->setTaxRate($product->getTaxRate());

        return $dto;
    }

    public function createFromObol(\Obol\Model\Product $productModel, Product $product = null)
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($productModel->getName());
        $product->setExternalReference($productModel->getId());
        $product->setPaymentProviderDetailsUrl($product->getPaymentProviderDetailsUrl());

        return $product;
    }
}
