<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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

    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private CancellationRequestRepositoryInterface $cancellationRequestRepository,
    ) {
    }

    public function process(CancellationRequest $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::CANCEL_SUBSCRIPTION, $this->cancellationRequestRepository);
    }
}
