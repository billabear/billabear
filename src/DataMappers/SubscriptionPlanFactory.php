<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\Dto\Generic\Api\SubscriptionPlan as ApiDto;
use App\Dto\Generic\App\Feature;
use App\Dto\Generic\App\Limit;
use App\Dto\Generic\App\Price;
use App\Dto\Generic\App\SubscriptionPlan as AppDto;
use App\Dto\Request\App\PostSubscriptionPlan;
use App\Dto\Request\App\Product\UpdateSubscriptionPlan;
use App\Entity\SubscriptionPlan;
use Doctrine\Common\Collections\Collection;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;

class SubscriptionPlanFactory
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        private PriceFactory $priceFactory,
        private FeatureFactory $featureFactory,
        private ProductFactory $productFactory,
    ) {
    }

    public function createFromPostSubscriptionPlan(PostSubscriptionPlan|UpdateSubscriptionPlan $dto, SubscriptionPlan $subscriptionPlan = null): SubscriptionPlan
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

        $subscriptionPlan->getPrices()->clear();
        /** @var Price $priceDto */
        foreach ($dto->getPrices() as $priceDto) {
            if (!$priceDto->hasId()) {
                continue;
            }
            $price = $this->priceRepository->getById($priceDto->getId());
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

    public function createAppDto(SubscriptionPlan $subscriptionPlan): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $subscriptionPlan->getId());
        $dto->setCodeName($subscriptionPlan->getCodeName());
        $dto->setName($subscriptionPlan->getName());
        $dto->setPerSeat($subscriptionPlan->isPerSeat());
        $dto->setPublic($subscriptionPlan->isPublic());
        $dto->setFree($subscriptionPlan->isFree());
        $dto->setUserCount($subscriptionPlan->getUserCount());
        $dto->setHasTrial($subscriptionPlan->getHasTrial());
        $dto->setTrialLengthDays($subscriptionPlan->getTrialLengthDays());
        $dto->setProduct($this->productFactory->createAppDtoFromProduct($subscriptionPlan->getProduct()));

        $priceEntities = $subscriptionPlan->getPrices() instanceof Collection ? $subscriptionPlan->getPrices()->toArray() : $subscriptionPlan->getPrices();
        $featuresEntities = $subscriptionPlan->getFeatures() instanceof Collection ? $subscriptionPlan->getFeatures()->toArray() : $subscriptionPlan->getFeatures();
        $limits = $subscriptionPlan->getLimits() instanceof Collection ? $subscriptionPlan->getLimits()->toArray() : $subscriptionPlan->getLimits();

        $pricesDto = array_map([$this->priceFactory, 'createAppDto'], $priceEntities);
        $featuresDto = array_map([$this->featureFactory, 'createAppDto'], $featuresEntities);
        $limitsDto = array_map([$this, 'createLimitDto'], $limits);

        $dto->setPrices($pricesDto);
        $dto->setFeatures($featuresDto);
        $dto->setLimits($limitsDto);

        return $dto;
    }

    public function createApiDto(SubscriptionPlan $subscriptionPlan): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $subscriptionPlan->getId());
        $dto->setName($subscriptionPlan->getName());
        $dto->setCodeName($subscriptionPlan->getCodeName());

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
