<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
