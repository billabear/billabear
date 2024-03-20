<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Payment;

use App\Entity\RefundCreatedProcess;
use App\Enum\WorkflowType;
use App\Repository\RefundCreatedProcessRepositoryInterface;
use App\Workflow\WorkflowProcessor;

class RefundCreatedProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private RefundCreatedProcessRepositoryInterface $refundCreatedProcessRepository,
    ) {
    }

    public function process(RefundCreatedProcess $refundCreatedProcess): void
    {
        $this->workflowProcessor->process($refundCreatedProcess, WorkflowType::CREATE_REFUND, $this->refundCreatedProcessRepository);
    }
}
