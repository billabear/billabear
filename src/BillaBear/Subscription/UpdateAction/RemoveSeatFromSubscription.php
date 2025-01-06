<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
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

class RemoveSeatFromSubscription
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionSeatModificationRepositoryInterface $subscriptionSeatModificationRepository,
        private ProviderInterface $provider,
        private SubscriptionFactoryInterface $subscriptionFactory,
    ) {
    }

    public function removeSeats(Subscription $subscription, int $seatsToRemove): void
    {
        $seatCount = $subscription->getSeats();
        $seatCount -= $seatsToRemove;
        $subscription->setSeats($seatCount);

        $seatModification = new SubscriptionSeatModification();
        $seatModification->setSubscription($subscription);
        $seatModification->setType(SubscriptionSeatModificationType::REMOVED);
        $seatModification->setChangeValue($seatsToRemove);
        $seatModification->setCreatedAt(new \DateTime());

        $this->subscriptionSeatModificationRepository->save($seatModification);
        $this->subscriptionRepository->save($subscription);

        $model = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);
        $this->provider->subscriptions()->updateSubscriptionSeats($model);
    }
}
