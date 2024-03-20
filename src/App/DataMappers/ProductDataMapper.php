<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
