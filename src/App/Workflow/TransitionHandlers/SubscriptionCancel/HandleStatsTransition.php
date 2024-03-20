<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\SubscriptionCancel;

use App\Entity\CancellationRequest;
use App\Stats\SubscriptionCancellationStats;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStatsTransition implements EventSubscriberInterface
{
    public function __construct(private SubscriptionCancellationStats $cancellationStats)
    {
    }

    public function transition(Event $event)
    {
        /** @var CancellationRequest $cancellationRequest */
        $cancellationRequest = $event->getSubject();

        $this->cancellationStats->handleStats($cancellationRequest->getSubscription());
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancel_subscription.transition.handle_stats' => ['transition'],
        ];
    }
}
