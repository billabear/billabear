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

use App\Dto\Request\App\Subscription\MassChange\CreateMassChange;
use App\Entity\MassSubscriptionChange as Entity;
use App\Enum\MassSubscriptionChangeStatus;
use App\Repository\SubscriptionPlanRepositoryInterface;
use App\User\UserProvider;

class MassChangeDataMapper
{
    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private UserProvider $userProvider,
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

        $entity->setCreatedAt(new \DateTime());
        $entity->setCreatedBy($this->userProvider->getUser());
        $entity->setStatus(MassSubscriptionChangeStatus::CREATED);

        return $entity;
    }
}
