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

namespace App\EventSubscriber;

use App\Entity\SubscriptionCreation;
use App\Repository\SubscriptionCreationRepositoryInterface;
use App\Subscription\SubscriptionCreationProcessor;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private SubscriptionCreationProcessor $subscriptionCreationProcessor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscriptionCreated::NAME => [
                'handleNewSubscription',
            ],
        ];
    }

    public function handleNewSubscription(SubscriptionCreated $subscriptionCreation): void
    {
        $subscriptionCreation = new SubscriptionCreation();
        $subscriptionCreation->setState('started');
        $subscriptionCreation->setSubscription($subscriptionCreation->getSubscription());
        $subscriptionCreation->setCreatedAt(new \DateTime());

        $this->subscriptionCreationRepository->save($subscriptionCreation);
        $this->subscriptionCreationProcessor->process($subscriptionCreation);
    }
}
