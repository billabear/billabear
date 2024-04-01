<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber\Subscription;

use App\Entity\SubscriptionSeatModification;
use App\Enum\SubscriptionSeatModificationType;
use App\Repository\SubscriptionSeatModificationRepositoryInterface;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PerSeatSubscriber implements EventSubscriberInterface
{
    public function __construct(private SubscriptionSeatModificationRepositoryInterface $subscriptionSeatModificationRepository)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscriptionCreated::NAME => [
                'handleNewSubscription',
            ],
        ];
    }

    public function handleNewSubscription(SubscriptionCreated $subscriptionCreated): void
    {
        $seatModification = new SubscriptionSeatModification();
        $seatModification->setSubscription($subscriptionCreated->getSubscription());
        $seatModification->setType(SubscriptionSeatModificationType::ADDED);
        $seatModification->setChangeValue($subscriptionCreated->getSubscription()->getSeats());
        $seatModification->setCreatedAt(new \DateTime());

        $this->subscriptionSeatModificationRepository->save($seatModification);
    }
}
