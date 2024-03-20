<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\SubscriptionCreation;

use App\Entity\SubscriptionCreation;
use App\Stats\SubscriptionCreationStats;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStats implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionCreationStats $creationStats,
    ) {
    }

    public function transition(Event $event)
    {
        $subscriptionCreation = $event->getSubject();

        if (!$subscriptionCreation instanceof SubscriptionCreation) {
            $this->getLogger()->error('Subscription creation transition has something other than a SubscriptionCreation object');

            return;
        }

        $this->creationStats->handleStats($subscriptionCreation->getSubscription());

        $this->getLogger()->info('Handled stats for subscription');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_subscription.transition.handle_stats' => ['transition'],
        ];
    }
}
