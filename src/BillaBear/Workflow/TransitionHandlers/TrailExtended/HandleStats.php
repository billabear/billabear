<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrailExtended;

use BillaBear\Entity\Processes\TrialConvertedProcess;
use BillaBear\Enum\CustomerStatus;
use BillaBear\Enum\CustomerSubscriptionEventType;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\SubscriptionCreationStats;
use BillaBear\Stats\TrialExtendedStats;
use BillaBear\Subscription\CustomerSubscriptionEventCreator;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class HandleStats implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerSubscriptionEventCreator $customerSubscriptionEventCreator,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private CustomerRepositoryInterface $customerRepository,
        private TrialExtendedStats $extendedStats,
        private SubscriptionCreationStats $creationStats,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialConvertedProcess $trialEnded */
        $trialEnded = $event->getSubject();
        $subscription = $trialEnded->getSubscription();
        $this->extendedStats->handleStats($subscription);
        $this->creationStats->handleStats($subscription);

        $customer = $subscription->getCustomer();
        $customer->setStatus(CustomerStatus::ACTIVE);
        $this->customerRepository->save($customer);

        $this->customerSubscriptionEventCreator->create(CustomerSubscriptionEventType::TRIAL_CONVERTED, $subscription->getCustomer(), $subscription);
        $this->getLogger()->info('Handled stats for trial extended', ['subscription_id' => (string) $subscription->getId()]);
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_extended.transition.handle_stats' => ['transition'],
        ];
    }
}
