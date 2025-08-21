<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\ChargeBackCreation;
use BillaBear\Repository\ChargeBackCreationRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;
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
