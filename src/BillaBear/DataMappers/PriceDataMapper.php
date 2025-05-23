<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
use BillaBear\Entity\Usage\Metric;
use BillaBear\Pricing\Usage\MetricAggregationMethod;
use BillaBear\Pricing\Usage\MetricEventIngestion;
use BillaBear\Pricing\Usage\MetricType;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Obol\Model\Enum\TierMode;
use Obol\Model\Enum\UsageType;
use Obol\Model\Metric as ObolMetric;
use Obol\Model\Tier;
use Parthenon\Billing\Enum\PriceType;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

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
        $dto = new ApiDto(
            (string) $price->getId(),
            $price->getAmount(),
            $price->getCurrency(),
            $price->getExternalReference(),
            $price->isRecurring(),
            $price->getSchedule(),
            $price->isIncludingTax(),
            $price->isPublic(),
            $price->getUsage(),
            $this->metricDataMapper->createApiDto($price->getMetric()),
            $price->getMetricType()?->value,
        );

        return $dto;
    }

    public function createPublicDto(Price $price): PublicDto
    {
        return new PublicDto(
            (string) $price->getId(),
            $price->getAmount(),
            $price->getCurrency(),
            $price->isRecurring(),
            $price->getSchedule(),
            $price->isIncludingTax(),
        );
    }

    public function createAppDto(?Price $price): ?AppDto
    {
        if (null === $price) {
            return null;
        }

        $dto = new AppDto(
            (string) $price->getId(),
            $price->getAmount(),
            $price->getCurrency(),
            $price->getExternalReference(),
            $price->isRecurring(),
            $price->isRecurring() ? $price->getSchedule() : 'one-off',
            $price->isIncludingTax(),
            $price->isPublic(),
            $price->getPaymentProviderDetailsUrl(),
            $price->getDisplayName(),
            $this->productDataMapper->createAppDtoFromProduct($price->getProduct()),
            $this->metricDataMapper->createAppDto($price->getMetric()),
            $price->getUsage(),
        );

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
        $price->setUsage(UsageType::METERED === $priceModel->getUsageType());
        $price->setMetricType($price->getUsage() ? MetricType::RESETTABLE : null);
        $price->setUnits($priceModel->getPackageAmount());

        if ($priceModel->getMetric()) {
            try {
                $metric = $this->metricRepository->getByCode($priceModel->getMetric()->getEventName());
            } catch (NoEntityFoundException) {
                $metric = $this->createMetric($priceModel->getMetric());
            }
            $price->setMetric($metric);
        }
        $tiers = [];
        /** @var Tier $tierModel */
        foreach ($priceModel->getTiers() as $tierModel) {
            $tier = $this->createTier($tierModel);
            $tier->setPrice($price);
            $tiers[] = $tier;
        }
        usort($tiers, function ($a, $b) {
            $a->getLastUnit() <=> $b->getLastUnit();
        });
        $lastTier = null;
        foreach ($tiers as $tier) {
            if ($lastTier) {
                $tier->setFirstUnit($lastTier->getLastUnit() + 1);
            } else {
                $tier->setFirstUnit(1);
            }
            $lastTier = $tier;
        }
        $price->setTierComponents($tiers);
        $price->setType($this->decidePriceType($priceModel));

        $product = $this->productRepository->getByExternalReference($priceModel->getProductReference());
        $price->setProduct($product);

        return $price;
    }

    protected function decidePriceType(\Obol\Model\Price $price): PriceType
    {
        if (!$price->isRecurring()) {
            return PriceType::ONE_OFF;
        }
        if (!empty($price->getTiers())) {
            if (TierMode::GRADUATED === $price->getTierMode()) {
                return PriceType::TIERED_GRADUATED;
            } else {
                return PriceType::TIERED_VOLUME;
            }
        }

        if ($price->getUsageType()) {
            if (!$price->getPackageAmount()) {
                return PriceType::UNIT;
            } else {
                return PriceType::PACKAGE;
            }
        }

        return PriceType::FIXED_PRICE;
    }

    protected function createTier(Tier $tier): TierComponent
    {
        $entity = new TierComponent();
        $entity->setLastUnit($tier->getUpTo());
        $entity->setUnitPrice($tier->getUnitAmount() ?? 0);
        $entity->setFlatFee($tier->getFlatAmount() ?? 0);

        return $entity;
    }

    protected function createMetric(ObolMetric $obolMetric): Metric
    {
        $metric = new Metric();
        $metric->setName($obolMetric->getDisplayName());
        $metric->setCode($obolMetric->getEventName());
        $eventIngestion = match ($obolMetric->getEventTimeWindow()) {
            'day' => MetricEventIngestion::DAILY,
            'hour' => MetricEventIngestion::HOURLY,
            default => MetricEventIngestion::REAL_TIME,
        };
        $metric->setEventIngestion($eventIngestion);
        $aggregationMethod = match ($obolMetric->getAggregation()) {
            'count' => MetricAggregationMethod::COUNT,
            default => MetricAggregationMethod::SUM,
        };
        $metric->setAggregationMethod($aggregationMethod);
        $metric->setCreatedAt(new \DateTime());

        return $metric;
    }
}
