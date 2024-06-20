<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrailEnded;

use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Enum\CustomerSubscriptionEventType;
use BillaBear\Repository\CustomerRepositoryInterface;
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
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialEndedProcess $trialEnded */
        $trialEnded = $event->getSubject();
        $subscription = $trialEnded->getSubscription();

        $this->customerSubscriptionEventCreator->create(CustomerSubscriptionEventType::TRIAL_ENDED, $subscription->getCustomer(), $subscription);
        $this->getLogger()->info('Handled stats for trial ended');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_ended.transition.handle_stats' => ['transition'],
        ];
    }
}
