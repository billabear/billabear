<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\EventSubscriber;

use App\Entity\SubscriptionCreation;
use App\Repository\SubscriptionCreationRepositoryInterface;
use App\Stats\RevenueEstimatesGeneration;
use App\Subscription\SubscriptionCreationProcessor;
use App\Webhook\Outbound\EventDispatcherInterface;
use App\Webhook\Outbound\Payload\StartSubscriptionPayload;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private SubscriptionCreationProcessor $subscriptionCreationProcessor,
        private RevenueEstimatesGeneration $revenueEstimatesGeneration,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscriptionCreated::NAME => [
                'handleNewSubscription',
            ],
            SubscriptionCancelled::NAME => [
                'adjustStats',
            ],
        ];
    }

    public function handleNewSubscription(SubscriptionCreated $subscriptionCreated): void
    {
        $subscriptionCreation = new SubscriptionCreation();
        $subscriptionCreation->setState('started');
        $subscriptionCreation->setSubscription($subscriptionCreated->getSubscription());
        $subscriptionCreation->setCreatedAt(new \DateTime());

        $this->subscriptionCreationRepository->save($subscriptionCreation);
        $this->subscriptionCreationProcessor->process($subscriptionCreation);

        $this->revenueEstimatesGeneration->generate();

        $this->eventDispatcher->dispatch(new StartSubscriptionPayload($subscriptionCreated->getSubscription()));
    }

    public function adjustStats(): void
    {
        $this->revenueEstimatesGeneration->generate();
    }
}
