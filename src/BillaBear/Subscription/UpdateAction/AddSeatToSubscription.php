<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\UpdateAction;

use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionSeatModification;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Repository\SubscriptionSeatModificationRepositoryInterface;
use BillaBear\Subscription\SubscriptionSeatModificationType;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\SubscriptionFactoryInterface;

class AddSeatToSubscription
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionSeatModificationRepositoryInterface $subscriptionSeatModificationRepository,
        private ProviderInterface $provider,
        private SubscriptionFactoryInterface $subscriptionFactory,
    ) {
    }

    public function addSeats(Subscription $subscription, int $seatsToAdd): void
    {
        $seatCount = $subscription->getSeats();
        $seatCount += $seatsToAdd;
        $subscription->setSeats($seatCount);

        $seatModification = new SubscriptionSeatModification();
        $seatModification->setSubscription($subscription);
        $seatModification->setType(SubscriptionSeatModificationType::ADDED);
        $seatModification->setChangeValue($seatsToAdd);
        $seatModification->setCreatedAt(new \DateTime());

        $this->subscriptionSeatModificationRepository->save($seatModification);
        $this->subscriptionRepository->save($subscription);

        $model = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);
        $this->provider->subscriptions()->updateSubscriptionSeats($model);
    }
}
