<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
        $cancellationRequest->setWhen($cancelSubscription->getProrate() ? ApiInputDto::WHEN_INSTANTLY : ApiInputDto::WHEN_END_OF_RUN);
        $cancellationRequest->setRefundType($cancelSubscription->getProrate() ? ApiInputDto::REFUND_PRORATE : ApiInputDto::REFUND_NONE);
        $cancellationRequest->setComment($cancelSubscription->getCancellationDetails()?->getComment());
        $cancellationRequest->setOriginalValidUntil($subscription->getValidUntil());
        $cancellationRequest->setState('started');
        $cancellationRequest->setCancellationType(CancellationType::CUSTOMER_REQUEST);

        return $cancellationRequest;
    }

    public function getCancellationRequestEntity(Subscription $subscription, AppInputDto|ApiInputDto $dto, ?BillingAdminInterface $user = null): Entity
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
