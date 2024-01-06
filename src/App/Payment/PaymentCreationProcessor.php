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
