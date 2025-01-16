<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Process;

use BillaBear\Entity\Processes\TrialConvertedProcess;
use BillaBear\Repository\Processes\TrialExtendedProcessRepositoryInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;

class TrialExtendedProcessor
{
    public function __construct(
        private WorkflowProcessor $workflowProcessor,
        private TrialExtendedProcessRepositoryInterface $trialExtendedProcessRepository,
    ) {
    }

    public function process(TrialConvertedProcess $request): void
    {
        $this->workflowProcessor->process($request, WorkflowType::TRIAL_CONVERTED, $this->trialExtendedProcessRepository);
    }
}
