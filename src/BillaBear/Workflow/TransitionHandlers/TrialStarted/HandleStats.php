<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrialStarted;

use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Processes\TrialStartedProcess;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\TrialStartedStats;
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
        private TrialStartedStats $startedStats,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialStartedProcess $trialStartedProcess */
        $trialStartedProcess = $event->getSubject();
        $subscription = $trialStartedProcess->getSubscription();
        $this->startedStats->handleStats($subscription);
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        $customer->setStatus(CustomerStatus::TRIAL_ACTIVE);
        $this->customerRepository->save($customer);

        $this->customerSubscriptionEventCreator->create(CustomerSubscriptionEventType::TRIAL_STARTED, $subscription->getCustomer(), $subscription);
        $this->getLogger()->info('Handled stats for trial started');
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.trial_started.transition.handle_stats' => ['transition'],
        ];
    }
}
