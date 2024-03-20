<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow;

use App\Enum\WorkflowType;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\Repository\RepositoryInterface;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class WorkflowProcessor
{
    use LoggerAwareTrait;

    public function __construct(private WorkflowBuilder $workflowBuilder)
    {
    }

    public function process(WorkflowProcessInterface $subject, WorkflowType $workflowType, RepositoryInterface $repository): WorkflowProcessInterface
    {
        $workflow = $this->workflowBuilder->build($workflowType);
        $subject->setHasError(false);
        try {
            foreach ($this->getTransitions($workflow) as $transition) {
                if ($workflow->can($subject, $transition)) {
                    $workflow->apply($subject, $transition);
                    $this->getLogger()->info('Did transition for workflow', ['workflow' => $workflowType->value, 'transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do transition for workflow", ['workflow' => $workflowType->value, 'transition' => $transition]);

                    return $subject;
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->info('Transition for workflow failed', ['workflow' => $workflowType->value, 'transition' => $transition, 'message' => $e->getMessage()]);
            $subject->setError($e->getMessage());
            $subject->setHasError(true);
        }

        $repository->save($subject);

        return $subject;
    }

    private function getTransitions(WorkflowInterface $workflow): array
    {
        $transitions = $workflow->getDefinition()->getTransitions();

        return array_map(function (Transition $transition) { return $transition->getName(); }, $transitions);
    }
}
