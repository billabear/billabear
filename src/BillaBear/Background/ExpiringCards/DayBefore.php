<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\ExpiringCards;

use BillaBear\Payment\Card\ExpiryChecker;
use BillaBear\Repository\Processes\ExpiringCardProcessRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class DayBefore
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly ExpiringCardProcessRepositoryInterface $expiringCardProcessRepository,
        private readonly ExpiryChecker $expiryChecker,
        private readonly WorkflowInterface $expiringCardProcessStateMachine,
    ) {
    }

    public function execute(): void
    {
        $this->getLogger()->info('Starting the before next charge check');
        $tomorrow = new \DateTime('+24 hours');

        $processes = $this->expiringCardProcessRepository->getActiveProccesses();

        foreach ($processes as $process) {
            foreach ($process->getCustomer()->getSubscriptions() as $subscription) {
                if ($subscription->getValidUntil() <= $tomorrow) {
                    $this->getLogger()->info("Handling customer's expiring card 24 hours before", ['customer_id' => (string) $process->getCustomer()->getId()]);
                    if ($this->expiryChecker->hasExpiredForSubscriptionCharge($process->getPaymentCard(), $subscription)) {
                        $this->expiringCardProcessStateMachine->apply($process, 'send_day_before_valid_email', ['subscription' => $subscription]);
                    } else {
                        $this->expiringCardProcessStateMachine->apply($process, 'send_day_before_not_valid_email', ['subscription' => $subscription]);
                    }
                    $process->setUpdatedAt(new \DateTime('now'));
                    $this->expiringCardProcessRepository->save($process);
                }
            }
        }
    }
}
