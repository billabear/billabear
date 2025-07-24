<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\DataMappers\Tax\TaxTypeDataMapper;
use BillaBear\Dto\Generic\Api\Product as ProductDto;
use BillaBear\Dto\Generic\App\Product as AppDto;
use BillaBear\Dto\Request\Api\CreateProduct as ApiCreate;
use BillaBear\Dto\Request\App\CreateProduct as AppCreate;
use BillaBear\Entity\Product;
use BillaBear\Repository\TaxTypeRepositoryInterface;

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

        $product->setName($createProduct->name);
        $product->setExternalReference($createProduct->external_reference);
        $product->setPhysical($createProduct->physical);
        if ($createProduct->tax_type) {
            $taxType = $this->taxTypeRepository->findById($createProduct->tax_type);
            $product->setTaxType($taxType);
        }

        return $product;
    }

    public function createFromAppCreate(AppCreate $createProduct, ?Product $product = null): Product
    {
        if (!$product) {
            $product = new Product();
        }

        $product->setName($createProduct->name);
        $product->setExternalReference($createProduct->externalReference);
        $product->setPhysical($createProduct->physical);
        if ($createProduct->taxRate) {
            $product->setTaxRate(floatval($createProduct->taxRate));
        }

        if ($createProduct->taxType) {
            $taxType = $this->taxTypeRepository->findById($createProduct->taxType);
            $product->setTaxType($taxType);
        }

        return $product;
    }

    public function createApiDtoFromProduct(Product $product): ProductDto
    {
        $dto = new ProductDto(
            (string) $product->getId(),
            $product->getName(),
            $product->getExternalReference(),
        );

        return $dto;
    }

    public function createAppDtoFromProduct(?Product $product): ?AppDto
    {
        if (!$product) {
            return null;
        }

        $dto = new AppDto(
            (string) $product->getId(),
            $product->getName(),
            $this->taxTypeDataMapper->createAppDto($product->getTaxType()),
            $product->getExternalReference(),
            $product->getPaymentProviderDetailsUrl(),
            $product->getTaxRate(),
            $product->getPhysical(),
        );

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
