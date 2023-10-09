<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
