<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Background\ExpiringCards;

use App\Payment\Card\ExpiryChecker;
use App\Repository\Processes\ExpiringCardProcessRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class DayBefore
{
    use LoggerAwareTrait;

    public function __construct(
        private ExpiringCardProcessRepositoryInterface $expiringCardProcessRepository,
        private ExpiryChecker $expiryChecker,
        private WorkflowInterface $expiringCardProcessStateMachine,
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
