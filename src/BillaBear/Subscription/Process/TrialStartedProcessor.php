<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Process;

use BillaBear\Entity\Processes\TrialStartedProcess;
use BillaBear\Repository\Processes\TrialStartedProcessRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;

class TrialStartedProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private TrialStartedProcessRepositoryInterface $trialStartedProcessRepository,
    ) {
    }

    public function process(TrialStartedProcess $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::TRIAL_STARTED, $this->trialStartedProcessRepository);
    }
}
