<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers\SubscriptionCancel;

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
            'workflow.cancel_subscription.transition.cancel_subscription' => ['transition'],
        ];
    }
}
