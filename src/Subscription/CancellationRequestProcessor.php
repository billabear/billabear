<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription;

use App\Entity\CancellationRequest;
use App\Repository\CancellationRequestRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Workflow\WorkflowInterface;

class CancellationRequestProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['cancel_subscription', 'issue_refund', 'handle_stats', 'send_customer_notice', 'send_internal_notice'];

    public function __construct(
        private WorkflowInterface $cancellationRequestStateMachine,
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
    ) {
    }

    public function process(CancellationRequest $request): void
    {
        $cancellationRequestStateMachine = $this->cancellationRequestStateMachine;

        $request->setHasError(false);
        try {
            foreach (self::TRANSITIONS as $transition) {
                if ($cancellationRequestStateMachine->can($request, $transition)) {
                    $cancellationRequestStateMachine->apply($request, $transition);

                    $this->getLogger()->info('Did cancellation request transition', ['transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do cancellation request transition", ['transition' => $transition]);
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
