<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\DataMappers;

use App\DataMappers\Subscriptions\SubscriptionDataMapper;
use App\Dto\Generic\App\CancellationRequest as AppDto;
use App\Dto\Interopt\Stripe\Requests\Subscriptions\CancelSubscription;
use App\Dto\Request\Api\Subscription\CancelSubscription as ApiInputDto;
use App\Dto\Request\App\CancelSubscription as AppInputDto;
use App\Entity\CancellationRequest as Entity;
use App\Enum\CancellationType;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Subscription;

class CancellationDataMapper
{
    public function __construct(
        private SubscriptionDataMapper $subscriptionDataMapper
    ) {
    }

    public function getCancellationRequestForStripe(Subscription $subscription, CancelSubscription $cancelSubscription): Entity
    {
        $cancellationRequest = new Entity();
        $cancellationRequest->setSubscription($subscription);
        $cancellationRequest->setCreatedAt(new \DateTime());
        $cancellationRequest->setWhen($cancelSubscription->getProrate() ? \App\Dto\Request\Api\Subscription\CancelSubscription::WHEN_INSTANTLY : \App\Dto\Request\Api\Subscription\CancelSubscription::WHEN_END_OF_RUN);
        $cancellationRequest->setRefundType($cancelSubscription->getProrate() ? \App\Dto\Request\Api\Subscription\CancelSubscription::REFUND_PRORATE : \App\Dto\Request\Api\Subscription\CancelSubscription::REFUND_NONE);
        $cancellationRequest->setComment($cancelSubscription->getCancellationDetails()?->getComment());
        $cancellationRequest->setOriginalValidUntil($subscription->getValidUntil());
        $cancellationRequest->setState('started');
        $cancellationRequest->setCancellationType(CancellationType::CUSTOMER_REQUEST);

        return $cancellationRequest;
    }

    public function getCancellationRequestEntity(Subscription $subscription, AppInputDto|ApiInputDto $dto, BillingAdminInterface $user = null): Entity
    {
        $cancellationRequest = new Entity();
        $cancellationRequest->setSubscription($subscription);
        if ($user) {
            $cancellationRequest->setBillingAdmin($user);
        }
        $cancellationRequest->setCreatedAt(new \DateTime());
        $cancellationRequest->setWhen($dto->getWhen());
        if ($dto->getDate()) {
            $cancellationRequest->setSpecificDate(new \DateTime($dto->getDate()));
        }
        $cancellationRequest->setRefundType($dto->getRefundType());
        $cancellationRequest->setComment($dto->getComment());
        $cancellationRequest->setOriginalValidUntil($subscription->getValidUntil());
        $cancellationRequest->setState('started');
        $cancellationRequest->setCancellationType(($dto instanceof AppInputDto) ? CancellationType::COMPANY_REQUEST : CancellationType::CUSTOMER_REQUEST);

        return $cancellationRequest;
    }

    public function createAppDto(Entity $entity): AppDto
    {
        $dto = new AppDto();
        $dto->setId((string) $entity->getId());
        $dto->setWhen($entity->getWhen());
        $dto->setRefundType($entity->getRefundType());
        $dto->setState($entity->getState());
        $dto->setCreatedAt($entity->getCreatedAt());
        $dto->setOriginalValidUntil($entity->getOriginalValidUntil());
        $dto->setComment($entity->getComment());
        $dto->setSpecificDate($entity->getSpecificDate());
        $dto->setHasError($entity->getHasError());
        $dto->setError($entity->getError());

        $dto->setSubscription($this->subscriptionDataMapper->createAppDto($entity->getSubscription()));

        return $dto;
    }
}
