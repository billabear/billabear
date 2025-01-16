<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber\Subscription;

use BillaBear\Entity\SubscriptionCreation;
use BillaBear\Repository\SubscriptionCreationRepositoryInterface;
use BillaBear\Stats\RevenueEstimatesGeneration;
use BillaBear\Subscription\Process\SubscriptionCreationProcessor;
use BillaBear\Webhook\Outbound\Payload\Subscription\SubscriptionStartPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionStatsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private SubscriptionCreationProcessor $subscriptionCreationProcessor,
        private RevenueEstimatesGeneration $revenueEstimatesGeneration,
        private WebhookDispatcherInterface $eventDispatcher,
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

        $this->eventDispatcher->dispatch(new SubscriptionStartPayload($subscriptionCreated->getSubscription()));
    }

    public function adjustStats(): void
    {
        $this->revenueEstimatesGeneration->generate();
    }
}
