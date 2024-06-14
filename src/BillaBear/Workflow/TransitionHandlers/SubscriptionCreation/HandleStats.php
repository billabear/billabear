<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\SubscriptionCreation;

use BillaBear\Entity\SubscriptionCreation;
use BillaBear\Enum\CustomerSubscriptionEventType;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\SubscriptionCreationStats;
use BillaBear\Subscription\CustomerSubscriptionEventCreator;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStats implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionCreationStats $creationStats,
        private CustomerSubscriptionEventCreator $customerSubscriptionEventCreator,
        private SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    public function transition(Event $event)
    {
        $subscriptionCreation = $event->getSubject();

        if (!$subscriptionCreation instanceof SubscriptionCreation) {
            $this->getLogger()->error('Subscription creation transition has something other than a SubscriptionCreation object');

            return;
        }
        $subscription = $subscriptionCreation->getSubscription();
        $this->creationStats->handleStats($subscription);

        $count = $this->subscriptionRepository->getAllActiveCountForCustomer($subscription->getCustomer());
        $cancelledCount = $this->subscriptionRepository->getAllCancelledCountForCustomer($subscription->getCustomer());

        if ($count > 1) {
            $eventType = CustomerSubscriptionEventType::ADDON_ADDED;
        } elseif ($cancelledCount > 0) {
            $eventType = CustomerSubscriptionEventType::REACTIVATED;
        } else {
            $eventType = CustomerSubscriptionEventType::ACTIVATED;
        }
        $this->customerSubscriptionEventCreator->create($eventType, $subscription->getCustomer(), $subscription);

        $this->getLogger()->info('Handled stats for subscription');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.create_subscription.transition.handle_stats' => ['transition'],
        ];
    }
}
