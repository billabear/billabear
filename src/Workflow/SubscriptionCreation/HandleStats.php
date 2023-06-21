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

namespace App\Workflow\SubscriptionCreation;

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
            'workflow.subscription_creation.transition.handle_stats' => ['transition'],
        ];
    }
}
