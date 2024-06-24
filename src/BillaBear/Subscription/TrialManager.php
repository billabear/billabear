<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Price;
use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Entity\Processes\TrialExtendedProcess;
use BillaBear\Entity\Processes\TrialStartedProcess;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Repository\Processes\TrialEndedProcessRepositoryInterface;
use BillaBear\Repository\Processes\TrialStartedProcessRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\Process\TrialEndedProcessor;
use BillaBear\Subscription\Process\TrialExtendedProcessor;
use BillaBear\Subscription\Process\TrialStartedProcessor;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Common\LoggerAwareTrait;

class TrialManager
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private TrialEndedProcessRepositoryInterface $trialEndedProcessRepository,
        private TrialStartedProcessor $trialStartedProcess,
        private SubscriptionFactory $subscriptionFactory,
        private TrialEndedProcessor $trialEndedProcessor,
        private TrialExtendedProcessor $trialExtendProcessor,
        private TrialStartedProcessRepositoryInterface $trialStartedProcessRepository,
    ) {
    }

    public function startTrial(Customer $customer, SubscriptionPlan $subscriptionPlan, ?int $seatNumber = null, ?int $trialLengthDays = null): Subscription
    {
        $subscription = $this->subscriptionFactory->create($customer, $subscriptionPlan, seatNumber: $seatNumber, hasTrial: true, trialLengthDays: $trialLengthDays);
        $this->subscriptionRepository->save($subscription);

        $process = new TrialStartedProcess();
        $process->setSubscription($subscription);
        $process->setCreatedAt(new \DateTime());
        $process->setState('started');
        $this->trialStartedProcessRepository->save($process);
        $this->trialStartedProcess->process($process);

        return $subscription;
    }

    public function extendTrial(Subscription $subscription, Price $price): Subscription
    {
        if ($price->isRecurring()) {
            $subscription->setPaymentSchedule($price->getSchedule());
        } else {
            $subscription->setPaymentSchedule('one-off');
        }
        $subscription->setPrice($price);
        $subscription->setMoneyAmount($price->getAsMoney());
        $subscription->setStatus(SubscriptionStatus::ACTIVE);

        // Don't charge them just now, wait until the trial is over.
        $this->subscriptionRepository->save($subscription);

        $process = new TrialExtendedProcess();
        $process->setSubscription($subscription);
        $process->setCreatedAt(new \DateTime());
        $process->setState('started');
        $this->trialEndedProcessRepository->save($process);

        $this->trialExtendProcessor->process($process);

        return $subscription;
    }

    public function endTrial(Subscription $subscription): void
    {
        $this->getLogger()->info('Ended trial for subscription', ['subscription_id' => (string) $subscription->getId()]);

        $subscription->setStatus(SubscriptionStatus::TRIAL_ENDED);
        $subscription->setActive(false);
        $this->subscriptionRepository->save($subscription);

        $process = new TrialEndedProcess();
        $process->setSubscription($subscription);
        $process->setCreatedAt(new \DateTime());
        $process->setState('started');
        $this->trialEndedProcessRepository->save($process);

        $this->trialEndedProcessor->process($process);
    }
}
