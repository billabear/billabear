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
