<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCancel;

use BillaBear\Entity\CancellationRequest;
use BillaBear\Enum\CustomerSubscriptionEventType;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\SubscriptionCancellationStats;
use BillaBear\Subscription\CustomerSubscriptionEventCreator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStatsTransition implements EventSubscriberInterface
{
    public function __construct(
        private SubscriptionCancellationStats $cancellationStats,
        private CustomerSubscriptionEventCreator $customerSubscriptionEventCreator,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var CancellationRequest $cancellationRequest */
        $cancellationRequest = $event->getSubject();

        $subscription = $cancellationRequest->getSubscription();
        $this->cancellationStats->handleStats($subscription);
        $count = $this->subscriptionRepository->getAllActiveCountForCustomer($subscription->getCustomer());

        if ($count > 0) {
            $eventType = CustomerSubscriptionEventType::ADDON_REMOVED;
        } else {
            $eventType = CustomerSubscriptionEventType::CHURNED;
        }
        $this->customerSubscriptionEventCreator->create($eventType, $subscription->getCustomer(), $subscription, $cancellationRequest->getBillingAdmin());
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.cancel_subscription.transition.handle_stats' => ['transition'],
        ];
    }
}
