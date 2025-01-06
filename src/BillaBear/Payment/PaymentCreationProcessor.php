<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\PaymentCreation;
use BillaBear\Repository\PaymentCreationRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;

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
