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

namespace App\Workflow\SubscriptionCancel;

use App\Dto\Request\App\CancelSubscription;
use App\Entity\CancellationRequest;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class CancelSubscriptionTransition implements EventSubscriberInterface
{
    public function __construct(private SubscriptionManagerInterface $subscriptionManager)
    {
    }

    public function transition(Event $event)
    {
        /** @var CancellationRequest $cancellationRequest */
        $cancellationRequest = $event->getSubject();
        $subscription = $cancellationRequest->getSubscription();

        if (CancelSubscription::WHEN_END_OF_RUN === $cancellationRequest->getWhen()) {
            $this->subscriptionManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);
        } elseif (CancelSubscription::WHEN_INSTANTLY === $cancellationRequest->getWhen()) {
            $this->subscriptionManager->cancelSubscriptionInstantly($subscription);
        } else {
            $this->subscriptionManager->cancelSubscriptionOnDate($subscription, $cancellationRequest->getSpecificDate());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancellation_request.transition.cancel_subscription' => ['transition'],
        ];
    }
}
