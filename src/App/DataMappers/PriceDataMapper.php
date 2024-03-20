<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\Price as ApiDto;
use App\Dto\Generic\App\Price as AppDto;
use App\Dto\Generic\Public\Price as PublicDto;
use App\Dto\Request\Api\CreatePrice;
use App\Entity\Price;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class PriceDataMapper
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductDataMapper $productDataMapper,
    ) {
    }

    public function createPriceFromDto(CreatePrice $createPrice, ?Price $price = null): Price
    {
        if (!$price) {
            $price = new Price();
        }

        $price->setAmount($createPrice->getAmount());
        $price->setCurrency($createPrice->getCurrency());

        if ($createPrice->hasExternalReference()) {
            $price->setExternalReference($createPrice->getExternalReference());
            $price->setPaymentProviderDetailsUrl(null);
        }

        $price->setPublic($createPrice->isPublic());
        $price->setRecurring($createPrice->isRecurring());
        $price->setSchedule($createPrice->getSchedule());
        $price->setIncludingTax($createPrice->isIncludingTax());
        $price->setCreatedAt(new \DateTime('now'));

        return $price;
    }

    public function createApiDto(Price $price): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $price->getId());
        $dto->setExternalReference($price->getExternalReference());
        $dto->setAmount($price->getAmount());
        $dto->setCurrency($price->getCurrency());
        $dto->setRecurring($price->isRecurring());
        $dto->setSchedule($price->getSchedule());
        $dto->setPublic($price->isPublic());

        return $dto;
    }

    public function createPublicDto(Price $price): PublicDto
    {
        $dto = new PublicDto();
        $dto->setId((string) $price->getId());
        $dto->setExternalReference($price->getExternalReference());
        $dto->setAmount($price->getAmount());
        $dto->setCurrency($price->getCurrency());
        $dto->setRecurring($price->isRecurring());
        $dto->setSchedule($price->getSchedule());
        $dto->setPublic($price->isPublic());

        return $dto;
    }

    public function createAppDto(?Price $price): ?AppDto
    {
        if (null === $price) {
            return null;
        }

        $dto = new AppDto();
        $dto->setId((string) $price->getId());
        $dto->setExternalReference($price->getExternalReference());
        $dto->setAmount($price->getAmount());
        $dto->setCurrency($price->getCurrency());
        $dto->setRecurring($price->isRecurring());
        $dto->setSchedule($price->isRecurring() ? $price->getSchedule() : 'one-off');
        $dto->setPublic($price->isPublic());
        $dto->setPaymentProviderDetailsUrl($price->getPaymentProviderDetailsUrl());
        $dto->setDisplayValue((string) $price->getAsMoney());
        $dto->setProduct($this->productDataMapper->createAppDtoFromProduct($price->getProduct()));

        return $dto;
    }

    public function createFromObol(\Obol\Model\Price $priceModel, ?Price $price = null)
    {
        if (!$price) {
            $price = new Price();
            $price->setCreatedAt(new \DateTime());
        }

        $price->setPublic(false);
        $price->setAmount($priceModel->getAmount());
        $price->setCurrency(strtoupper($priceModel->getCurrency()));
        $price->setRecurring($priceModel->isRecurring());
        $price->setSchedule($priceModel->getSchedule());
        $price->setExternalReference($priceModel->getId());
        $price->setPaymentProviderDetailsUrl($priceModel->getUrl());

        $product = $this->productRepository->getByExternalReference($priceModel->getProductReference());
        $price->setProduct($product);

        return $price;
    }
}
