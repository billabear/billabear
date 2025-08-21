<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber\Subscription;

use BillaBear\Entity\SubscriptionSeatModification;
use BillaBear\Repository\SubscriptionSeatModificationRepositoryInterface;
use BillaBear\Subscription\SubscriptionSeatModificationType;
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
