<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\TransitionHandlers\TrailEnded;

use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\TrialEndedStats;
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
        private TrialEndedStats $endedStats,
    ) {
    }

    public function transition(Event $event)
    {
        /** @var TrialEndedProcess $trialEnded */
        $trialEnded = $event->getSubject();
        $subscription = $trialEnded->getSubscription();

        $customer = $subscription->getCustomer();
        $customer->setStatus(CustomerStatus::TRIAL_ENDED);
        $this->customerRepository->save($customer);

        $this->endedStats->handleStats($subscription);
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
