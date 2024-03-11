<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\Product as ProductDto;
use App\Dto\Generic\App\Product as AppDto;
use App\Dto\Request\Api\CreateProduct as ApiCreate;
use App\Dto\Request\App\CreateProduct as AppCreate;
use App\Entity\Product;
use App\Repository\TaxTypeRepositoryInterface;

class ProductDataMapper
{
    public function __construct(
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private TaxTypeDataMapper $taxTypeDataMapper,
    ) {
    }

    public function createFromApiCreate(ApiCreate $createProduct, ?Product $product = null): Product
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($createProduct->getName());
        $product->setExternalReference($createProduct->getExternalReference());
        if ($createProduct->getTaxType()) {
            $taxType = $this->taxTypeRepository->findById($createProduct->getTaxType());
            $product->setTaxType($taxType);
        }

        return $product;
    }

    public function createFromAppCreate(AppCreate $createProduct, ?Product $product = null): Product
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($createProduct->getName());
        $product->setExternalReference($createProduct->getExternalReference());
        if ($createProduct->getTaxRate()) {
            $product->setTaxRate(floatval($createProduct->getTaxRate()));
        }

        if ($createProduct->getTaxType()) {
            $taxType = $this->taxTypeRepository->findById($createProduct->getTaxType());
            $product->setTaxType($taxType);
        }

        return $product;
    }

    public function createApiDtoFromProduct(Product $product): ProductDto
    {
        $dto = new ProductDto();
        $dto->setId((string) $product->getId());
        $dto->setName($product->getName());
        $dto->setExternalReference($product->getExternalReference());

        return $dto;
    }

    public function createAppDtoFromProduct(Product $product): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $product->getId());
        $dto->setName($product->getName());
        $dto->setExternalReference($product->getExternalReference());
        $dto->setPaymentProviderDetailsUrl($product->getPaymentProviderDetailsUrl());
        $dto->setTaxType($this->taxTypeDataMapper->createAppDto($product->getTaxType()));
        $dto->setTaxRate($product->getTaxRate());

        return $dto;
    }

    public function createFromObol(\Obol\Model\Product $productModel, ?Product $product = null)
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
