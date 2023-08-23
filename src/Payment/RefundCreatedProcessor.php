<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment;

use App\Entity\RefundCreatedProcess;
use App\Repository\RefundCreatedProcessRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class RefundCreatedProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['handle_stats', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowInterface $refundCreatedProcessStateMachine,
        private RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
    ) {
    }

    public function process(RefundCreatedProcess $refundCreatedProcess): void
    {
        $refundCreatedProcessStateMachine = $this->refundCreatedProcessStateMachine;

        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($refundCreatedProcessStateMachine->can($refundCreatedProcess, $transition)) {
                    $refundCreatedProcessStateMachine->apply($refundCreatedProcess, $transition);

                    $this->getLogger()->info('Did refund creation transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do refund creation transition", ['transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Refund creation transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $refundCreatedProcess->setError($e->getMessage());
        }

        $this->refundCreatedProcessRepository->save($refundCreatedProcess);
    }
}
