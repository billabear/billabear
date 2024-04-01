<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\EventSubscriber\Subscription;

use App\Entity\SubscriptionCreation;
use App\Repository\SubscriptionCreationRepositoryInterface;
use App\Stats\RevenueEstimatesGeneration;
use App\Subscription\SubscriptionCreationProcessor;
use App\Webhook\Outbound\EventDispatcherInterface;
use App\Webhook\Outbound\Payload\StartSubscriptionPayload;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionStatsSubscriber implements EventSubscriberInterface
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
