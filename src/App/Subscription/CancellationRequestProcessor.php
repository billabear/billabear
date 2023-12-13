<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Subscription;

use App\Entity\CancellationRequest;
use App\Enum\WorkflowType;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Workflow\WorkflowBuilder;
use Parthenon\Common\LoggerAwareTrait;

class CancellationRequestProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['cancel_subscription', 'issue_refund', 'handle_stats', 'send_customer_notice', 'send_internal_notice', 'complete'];

    public function __construct(
        private WorkflowBuilder $builder,
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
    ) {
    }

    public function process(CancellationRequest $request): void
    {
        $cancellationRequestStateMachine = $this->builder->build(WorkflowType::CANCEL_SUBSCRIPTION);
        $request->setHasError(false);
        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($cancellationRequestStateMachine->can($request, $transition)) {
                    $cancellationRequestStateMachine->apply($request, $transition);
                    $this->getLogger()->info('Did cancellation request transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do cancellation request transition", ['transition' => $transition]);

                    return;
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Cancellation request transition failed', ['transition' => $transition, 'message' => $e->getMessage()]);
            $request->setError($e->getMessage());
            $request->setHasError(true);
        }

        $this->cancellationRequestRepository->save($request);
    }
}
