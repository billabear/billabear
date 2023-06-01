<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Factory;

use App\Dto\Generic\Api\Price as ApiDto;
use App\Dto\Generic\App\Price as AppDto;
use App\Dto\Request\Api\CreatePrice;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class PriceFactory
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function createPriceFromDto(CreatePrice $createPrice, Price $price = null): Price
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

    public function createAppDto(Price $price): AppDto
    {
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

        return $dto;
    }

    public function createFromObol(\Obol\Model\Price $priceModel, Price $price = null)
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
