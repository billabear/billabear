<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Entity\CancellationRequest;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\LoggerAwareTrait;

class CancellationRequestProcessor
{
    use LoggerAwareTrait;

    public const TRANSITIONS = ['cancel_subscription', 'issue_refund', 'handle_stats', 'send_customer_notice', 'send_internal_notice', 'complete'];

    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
    ) {
    }

    public function process(CancellationRequest $request): void
    {
        $cancellationRequestStateMachine = $this->workflowProcessor->process($request, WorkflowType::CANCEL_SUBSCRIPTION, $this->cancellationRequestRepository);
    }
}
