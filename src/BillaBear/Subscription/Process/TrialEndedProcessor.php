<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Process;

use BillaBear\Entity\Processes\TrialEndedProcess;
use BillaBear\Enum\WorkflowType;
use BillaBear\Repository\Processes\TrialEndedProcessRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;

class TrialEndedProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private TrialEndedProcessRepositoryInterface $trialEndedProcessRepository,
    ) {
    }

    public function process(TrialEndedProcess $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::TRIAL_ENDED, $this->trialEndedProcessRepository);
    }
}
