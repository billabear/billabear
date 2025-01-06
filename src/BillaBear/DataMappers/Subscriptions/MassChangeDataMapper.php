<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Subscriptions;

use BillaBear\DataMappers\PriceDataMapper;
use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\Dto\Request\App\Subscription\MassChange\CreateMassChange;
use BillaBear\Dto\Request\App\Subscription\MassChange\EstimateMassChange;
use BillaBear\Dto\Response\App\Subscription\MassChange\MassSubscriptionChange as AppDto;
use BillaBear\Entity\MassSubscriptionChange as Entity;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\SubscriptionPlanRepositoryInterface;
use BillaBear\Subscription\MassSubscriptionChangeStatus;
use BillaBear\User\UserProvider;
use Parthenon\Billing\Repository\PriceRepositoryInterface;

class MassChangeDataMapper
{
    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private UserProvider $userProvider,
        private BrandSettingsDataMapper $brandSettingsDataMapper,
        private SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
        private PriceDataMapper $priceDataMapper,
    ) {
    }

    public function createEntity(CreateMassChange|EstimateMassChange $dto): Entity
    {
        $entity = new Entity();

        if ($dto instanceof CreateMassChange) {
            $changeDate = \DateTime::createFromFormat(DATE_RFC3339_EXTENDED, $dto->getChangeDate());
            $entity->setChangeDate($changeDate);
        }

        if ($dto->getNewPlan()) {
            $subscriptionPlan = $this->subscriptionPlanRepository->findById($dto->getNewPlan());
            $entity->setNewSubscriptionPlan($subscriptionPlan);
        }
        if ($dto->getTargetPlan()) {
            $subscriptionPlan = $this->subscriptionPlanRepository->findById($dto->getTargetPlan());
            $entity->setTargetSubscriptionPlan($subscriptionPlan);
        }
        if ($dto->getNewPrice()) {
            $newPrice = $this->priceRepository->findById($dto->getNewPrice());
            $entity->setNewPrice($newPrice);
        }
        if ($dto->getTargetPrice()) {
            $targetPrice = $this->priceRepository->findById($dto->getTargetPrice());
            $entity->setTargetPrice($targetPrice);
        }
        if ($dto->getTargetBrand()) {
            $brand = $this->brandSettingsRepository->getByCode($dto->getTargetBrand());
            $entity->setBrandSettings($brand);
        }

        $entity->setTargetCountry($dto->getTargetCountry());
        $entity->setCreatedAt(new \DateTime());
        $entity->setCreatedBy($this->userProvider->getUser());
        $entity->setStatus(MassSubscriptionChangeStatus::CREATED);

        return $entity;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setTargetBrandSettings($this->brandSettingsDataMapper->createAppDto($entity->getBrandSettings()));
        $dto->setTargetPlan($this->subscriptionPlanDataMapper->createAppDto($entity->getTargetSubscriptionPlan()));
        $dto->setNewPlan($this->subscriptionPlanDataMapper->createAppDto($entity->getNewSubscriptionPlan()));
        $dto->setTargetPrice($this->priceDataMapper->createAppDto($entity->getTargetPrice()));
        $dto->setNewPrice($this->priceDataMapper->createAppDto($entity->getNewPrice()));
        $dto->setTargetCountry($entity->getTargetCountry());
        $dto->setStatus($entity->getStatus()->value);
        $dto->setChangeDate($entity->getChangeDate());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }
}
