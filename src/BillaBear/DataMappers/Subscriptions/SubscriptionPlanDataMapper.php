<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Subscriptions;

use BillaBear\DataMappers\FeatureDataMapper;
use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\DataMappers\ProductDataMapper;
use BillaBear\Dto\Generic\Api\SubscriptionPlan as ApiDto;
use BillaBear\Dto\Generic\App\Feature;
use BillaBear\Dto\Generic\App\Limit;
use BillaBear\Dto\Generic\App\Price;
use BillaBear\Dto\Generic\App\SubscriptionPlan as AppDto;
use BillaBear\Dto\Generic\Public\SubscriptionPlan as PublicDto;
use BillaBear\Dto\Request\App\Product\UpdateSubscriptionPlan;
use BillaBear\Dto\Request\App\Subscription\PostSubscriptionPlan;
use BillaBear\Entity\SubscriptionPlan;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;

class SubscriptionPlanDataMapper
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        private PriceDataMapper $priceFactory,
        private FeatureDataMapper $featureFactory,
        private ProductDataMapper $productFactory,
    ) {
    }

    public function createFromPostSubscriptionPlan(PostSubscriptionPlan|UpdateSubscriptionPlan $dto, ?SubscriptionPlan $subscriptionPlan = null): SubscriptionPlan
    {
        if (!$subscriptionPlan) {
            $subscriptionPlan = new SubscriptionPlan();
        }

        $subscriptionPlan->setName($dto->getName());
        $subscriptionPlan->setCodeName($dto->getCodeName());
        $subscriptionPlan->setPublic($dto->getPublic());
        $subscriptionPlan->setPerSeat($dto->getPerSeat());
        $subscriptionPlan->setUserCount($dto->getUserCount());
        $subscriptionPlan->setFree($dto->getFree());
        $subscriptionPlan->setHasTrial($dto->getHasTrial());
        $subscriptionPlan->setTrialLengthDays($dto->getTrialLengthDays());
        $subscriptionPlan->setIsTrialStandalone($dto->getIsTrialStandalone());

        $subscriptionPlan->getPrices()->clear();
        /** @var Price $priceDto */
        foreach ($dto->getPrices() as $priceDto) {
            if (!$priceDto->id) {
                continue;
            }
            $price = $this->priceRepository->getById($priceDto->id);
            $subscriptionPlan->addPrice($price);
        }

        $subscriptionPlan->getFeatures()->clear();
        /** @var Feature $featureDto */
        foreach ($dto->getFeatures() as $featureDto) {
            if (!$featureDto->hasId()) {
                continue;
            }
            $feature = $this->subscriptionFeatureRepository->getById($featureDto->getId());
            $subscriptionPlan->addFeature($feature);
        }

        $subscriptionPlan->getLimits()->clear();
        /** @var Limit $limitDto */
        foreach ($dto->getLimits() as $limitDto) {
            $feature = $this->subscriptionFeatureRepository->getById($limitDto->getFeature()->getId());

            $limit = new SubscriptionPlanLimit();
            $limit->setSubscriptionFeature($feature);
            $limit->setSubscriptionPlan($subscriptionPlan);
            $limit->setLimit($limitDto->getLimit());
            $subscriptionPlan->addLimit($limit);
        }

        return $subscriptionPlan;
    }

    public function createPublicDto(SubscriptionPlan $subscriptionPlan): PublicDto
    {
        return new PublicDto(
            (string) $subscriptionPlan->getId(),
            $subscriptionPlan->getName(),
            $subscriptionPlan->getCodeName(),
            $subscriptionPlan->getUserCount(),
            $subscriptionPlan->isPerSeat(),
            $subscriptionPlan->getHasTrial(),
            $subscriptionPlan->getTrialLengthDays(),
            $subscriptionPlan->isFree(),
            $subscriptionPlan->getIsTrialStandalone()
        );
    }

    public function createAppDto(?SubscriptionPlan $subscriptionPlan): ?AppDto
    {
        if (null === $subscriptionPlan) {
            return null;
        }

        $priceEntities = $subscriptionPlan->getPrices() instanceof Collection ? $subscriptionPlan->getPrices()->toArray() : $subscriptionPlan->getPrices();
        $featuresEntities = $subscriptionPlan->getFeatures() instanceof Collection ? $subscriptionPlan->getFeatures()->toArray() : $subscriptionPlan->getFeatures();
        $limits = $subscriptionPlan->getLimits() instanceof Collection ? $subscriptionPlan->getLimits()->toArray() : $subscriptionPlan->getLimits();

        $pricesDto = array_map([$this->priceFactory, 'createAppDto'], $priceEntities);
        $featuresDto = array_map([$this->featureFactory, 'createAppDto'], $featuresEntities);
        $limitsDto = array_map([$this, 'createLimitDto'], $limits);

        $dto = new AppDto(
            (string) $subscriptionPlan->getId(),
            $subscriptionPlan->getName(),
            $subscriptionPlan->getCodeName(),
            $subscriptionPlan->getUserCount(),
            $subscriptionPlan->isPerSeat(),
            $subscriptionPlan->getHasTrial(),
            $subscriptionPlan->getTrialLengthDays(),
            $subscriptionPlan->isFree(),
            $subscriptionPlan->isPublic(),
            $featuresDto,
            $pricesDto,
            $limitsDto,
            $this->productFactory->createAppDtoFromProduct($subscriptionPlan->getProduct()),
            $subscriptionPlan->getIsTrialStandalone(),
        );

        return $dto;
    }

    public function copyAppDtoWithNewPrices(AppDto $old, array $prices): AppDto
    {
        return new AppDto(
            $old->id,
            $old->name,
            $old->codeName,
            $old->userCount,
            $old->perSeat,
            $old->hasTrial,
            $old->trialLengthDays,
            $old->free,
            $old->public,
            $old->features,
            $prices,
            $old->limits,
            $old->product,
            $old->isTrialStandalone,
        );
    }

    public function createApiDto(SubscriptionPlan $subscriptionPlan): ApiDto
    {
        $priceEntities = $subscriptionPlan->getPrices() instanceof Collection ? $subscriptionPlan->getPrices()->toArray() : $subscriptionPlan->getPrices();
        $featuresEntities = $subscriptionPlan->getFeatures() instanceof Collection ? $subscriptionPlan->getFeatures()->toArray() : $subscriptionPlan->getFeatures();
        $limits = $subscriptionPlan->getLimits() instanceof Collection ? $subscriptionPlan->getLimits()->toArray() : $subscriptionPlan->getLimits();

        $pricesDto = array_map([$this->priceFactory, 'createAppDto'], $priceEntities);
        $featuresDto = array_map([$this->featureFactory, 'createAppDto'], $featuresEntities);
        $limitsDto = array_map([$this, 'createLimitDto'], $limits);

        $dto = new ApiDto(
            (string) $subscriptionPlan->getId(),
            $subscriptionPlan->getName(),
            $subscriptionPlan->getCodeName(),
            $subscriptionPlan->getUserCount(),
            $subscriptionPlan->isPerSeat(),
            $subscriptionPlan->getHasTrial(),
            $subscriptionPlan->getTrialLengthDays(),
            $subscriptionPlan->isFree(),
            $subscriptionPlan->isPublic(),
            $featuresDto,
            $pricesDto,
            $limitsDto,
            $this->productFactory->createApiDtoFromProduct($subscriptionPlan->getProduct()),
            $subscriptionPlan->getIsTrialStandalone(),
        );

        return $dto;
    }

    private function createLimitDto(SubscriptionPlanLimit $subscriptionPlanLimit): Limit
    {
        $dto = new Limit();
        $dto->setFeature($this->featureFactory->createAppDto($subscriptionPlanLimit->getSubscriptionFeature()));
        $dto->setLimit($subscriptionPlanLimit->getLimit());

        return $dto;
    }
}
