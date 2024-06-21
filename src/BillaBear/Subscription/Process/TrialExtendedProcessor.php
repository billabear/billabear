<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Process;

use BillaBear\Entity\Processes\TrialExtendedProcess;
use BillaBear\Enum\WorkflowType;
use BillaBear\Repository\Processes\TrialExtendedProcessRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;

class TrialExtendedProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private TrialExtendedProcessRepositoryInterface $trialExtendedProcessRepository,
    ) {
    }

    public function process(TrialExtendedProcess $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::TRIAL_EXTENDED, $this->trialExtendedProcessRepository);
    }
}
