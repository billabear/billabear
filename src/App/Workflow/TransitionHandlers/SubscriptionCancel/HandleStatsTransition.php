<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
