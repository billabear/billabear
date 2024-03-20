<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Payment;

use App\Entity\ChargeBackCreation;
use App\Enum\WorkflowType;
use App\Repository\ChargeBackCreationRepositoryInterface;
use App\Workflow\WorkflowProcessor;
use Parthenon\Common\LoggerAwareTrait;

class ChargeBackCreationProcessor
{
    use LoggerAwareTrait;

    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private ChargeBackCreationRepositoryInterface $chargeBackCreationRepository,
    ) {
    }

    public function process(ChargeBackCreation $chargeBackCreation): void
    {
        $this->workflowProcessor->process($chargeBackCreation, WorkflowType::CREATE_CHARGEBACK, $this->chargeBackCreationRepository);
    }
}
