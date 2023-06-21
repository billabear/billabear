<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\SubscriptionCancel;

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
            'workflow.cancellation_request.transition.handle_stats' => ['transition'],
        ];
    }
}
