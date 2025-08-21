<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\EventSubscriber\Subscription;

use BillaBear\Entity\SubscriptionCreation;
use BillaBear\Repository\SubscriptionCreationRepositoryInterface;
use BillaBear\Stats\RevenueEstimatesGeneration;
use BillaBear\Webhook\Outbound\Payload\Subscription\SubscriptionStartPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessSubscriptionCreated;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionStatsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SubscriptionCreationRepositoryInterface $subscriptionCreationRepository,
        private RevenueEstimatesGeneration $revenueEstimatesGeneration,
        private WebhookDispatcherInterface $eventDispatcher,
        private MessageBusInterface $messageBus,
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
        $this->messageBus->dispatch(new ProcessSubscriptionCreated((string) $subscriptionCreation->getId()));

        $this->revenueEstimatesGeneration->generate();

        $this->eventDispatcher->dispatch(new SubscriptionStartPayload($subscriptionCreated->getSubscription()));
    }

    public function adjustStats(): void
    {
        $this->revenueEstimatesGeneration->generate();
    }
}
