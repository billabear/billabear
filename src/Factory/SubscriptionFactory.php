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

use App\Customer\CustomerFactory;
use App\Dto\Generic\Api\Subscription as ApiDto;
use App\Dto\Generic\App\Subscription as AppDto;
use Parthenon\Billing\Entity\Subscription as Entity;

class SubscriptionFactory
{
    public function __construct(
        private SubscriptionPlanFactory $subscriptionPlanFactory,
        private PriceFactory $priceFactory,
        private CustomerFactory $customerFactory,
    ) {
    }

    public function createAppDto(Entity $subscription): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $subscription->getId());
        $dto->setStatus($subscription->getStatus());
        $dto->setSchedule($subscription->getPaymentSchedule());
        $dto->setSubscriptionPlan($this->subscriptionPlanFactory->createAppDto($subscription->getSubscriptionPlan()));
        $dto->setPrice($this->priceFactory->createAppDto($subscription->getPrice()));
        $dto->setChildExternalReference($subscription->getChildExternalReference());
        $dto->setExternalMainReference($subscription->getMainExternalReference());
        $dto->setExternalMainReferenceDetailsUrl($subscription->getMainExternalReferenceDetailsUrl());
        $dto->setCreatedAt($subscription->getCreatedAt());
        $dto->setUpdatedAt($subscription->getUpdatedAt());
        $dto->setValidUntil($subscription->getValidUntil());
        $dto->setCustomer($this->customerFactory->createAppDtoFromCustomer($subscription->getCustomer()));

        return $dto;
    }

    public function createApiDto(Entity $subscription): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $subscription->getId());
        $dto->setSubscriptionPlan($this->subscriptionPlanFactory->createAppDto($subscription->getSubscriptionPlan()));
        $dto->setPrice($this->priceFactory->createAppDto($subscription->getPrice()));
        $dto->setChildExternalReference($subscription->getChildExternalReference());
        $dto->setExternalMainReference($subscription->getMainExternalReference());
        $dto->setExternalMainReferenceDetailsUrl($subscription->getMainExternalReferenceDetailsUrl());
        $dto->setCreatedAt($subscription->getCreatedAt());
        $dto->setUpdatedAt($subscription->getUpdatedAt());
        $dto->setValidUntil($subscription->getValidUntil());

        return $dto;
    }
}
