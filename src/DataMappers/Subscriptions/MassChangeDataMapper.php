<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers\Subscriptions;

use App\DataMappers\PriceDataMapper;
use App\DataMappers\Settings\BrandSettingsDataMapper;
use App\DataMappers\SubscriptionPlanDataMapper;
use App\Dto\Request\App\Subscription\MassChange\CreateMassChange;
use App\Dto\Response\App\Subscription\MassChange\MassSubscriptionChange as AppDto;
use App\Entity\MassSubscriptionChange as Entity;
use App\Enum\MassSubscriptionChangeStatus;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\SubscriptionPlanRepositoryInterface;
use App\User\UserProvider;
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

    public function createEntity(CreateMassChange $dto): Entity
    {
        $entity = new Entity();

        $changeDate = \DateTime::createFromFormat(DATE_RFC3339_EXTENDED, $dto->getChangeDate());
        $entity->setChangeDate($changeDate);

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
