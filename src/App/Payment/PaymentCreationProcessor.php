<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Payment;

use App\Entity\PaymentCreation;
use App\Enum\WorkflowType;
use App\Repository\PaymentCreationRepositoryInterface;
use App\Workflow\WorkflowProcessor;

class PaymentCreationProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private PaymentCreationRepositoryInterface $paymentCreationRepository,
    ) {
    }

    public function process(PaymentCreation $paymentCreation): void
    {
        $this->workflowProcessor->process($paymentCreation, WorkflowType::CREATE_PAYMENT, $this->paymentCreationRepository);
    }
}
