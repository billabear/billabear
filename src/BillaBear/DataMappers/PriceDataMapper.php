<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\DataMappers\Usage\MetricDataMapper;
use BillaBear\Dto\Generic\Api\Price as ApiDto;
use BillaBear\Dto\Generic\App\Price as AppDto;
use BillaBear\Dto\Generic\Public\Price as PublicDto;
use BillaBear\Dto\Request\Api\CreatePrice;
use BillaBear\Dto\Request\Api\Price\CreateTier;
use BillaBear\Entity\Price;
use BillaBear\Entity\TierComponent;
use BillaBear\Enum\MetricType;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Parthenon\Billing\Enum\PriceType;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class PriceDataMapper
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductDataMapper $productDataMapper,
        private MetricRepositoryInterface $metricRepository,
        private MetricDataMapper $metricDataMapper,
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
        if ($createPrice->getType()) {
            $type = PriceType::from($createPrice->getType());
            $price->setType($type);
        } else {
            // Legacy. Maybe remove since it's so early in? TODO check if it breaks anything
            $price->setType($createPrice->isRecurring() ? PriceType::FIXED_PRICE : PriceType::ONE_OFF);
        }
        $price->setSchedule($createPrice->getSchedule());
        $price->setIncludingTax($createPrice->isIncludingTax());
        $price->setUnits($createPrice->getUnits());
        $price->setUsage($createPrice->getUsage());
        if ($price->getUsage()) {
            $price->setMetric($this->metricRepository->getById($createPrice->getMetric()));
            $price->setMetricType(MetricType::from($createPrice->getMetricType()));
        }
        $price->setCreatedAt(new \DateTime('now'));

        $tiers = [];

        /** @var CreateTier $tier */
        foreach ($createPrice->getTiers() as $tier) {
            $component = new TierComponent();
            $component->setPrice($price);
            $component->setFirstUnit($tier->getFirstUnit());
            $component->setLastUnit($tier->getLastUnit());
            $component->setUnitPrice($tier->getUnitPrice());
            $component->setFlatFee($tier->getFlatFee());
            $tiers[] = $component;
        }

        $price->setTierComponents(new ArrayCollection($tiers));

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
        $dto->setIncludingTax($price->isIncludingTax());

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
        $dto->setIncludingTax($price->isIncludingTax());

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
        if ($price->getAmount()) {
            $dto->setDisplayValue((string) $price->getAsMoney());
        }
        $dto->setProduct($this->productDataMapper->createAppDtoFromProduct($price->getProduct()));
        $dto->setIncludingTax($price->isIncludingTax());
        $dto->setMetric($this->metricDataMapper->createAppDto($price->getMetric()));

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
