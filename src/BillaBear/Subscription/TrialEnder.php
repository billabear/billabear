<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\Processes\TrialEndedProcessRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\Process\TrialEndedProcessor;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Common\LoggerAwareTrait;

class TrialEnder
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private TrialEndedProcessRepositoryInterface $trialEndedProcessRepository,
        private TrialEndedProcessor $trialEndedProcessor,
    ) {
    }

    public function endTrial(Subscription $subscription)
    {
        $this->getLogger()->info('Ended trial for subscription', ['subscription_id' => (string) $subscription->getId()]);

        $subscription->setStatus(SubscriptionStatus::TRIAL_ENDED);
        $this->subscriptionRepository->save($subscription);

        $process = new TrialEndedProcess();
        $process->setSubscription($subscription);
        $process->setCreatedAt(new \DateTime());
        $process->setState('started');
        $this->trialEndedProcessRepository->save($process);

        $this->trialEndedProcessor->process($process);
    }
}
