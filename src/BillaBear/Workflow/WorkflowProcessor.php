<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow;

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
        $subject->setError(null);
        try {
            foreach ($this->getTransitions($workflow) as $transition) {
                if ($workflow->can($subject, $transition)) {
                    $workflow->apply($subject, $transition);
                    $this->getLogger()->info('Did transition for workflow', ['workflow' => $workflowType->value, 'transition' => $transition]);
                } else {
                    $this->getLogger()->info("Can't do transition for workflow", ['workflow' => $workflowType->value, 'transition' => $transition]);
                }
            }
        } catch (\Throwable $e) {
            $this->getLogger()->warning('Transition for workflow failed', ['workflow' => $workflowType->value, 'transition' => $transition ?? 'unknown', 'message' => $e->getMessage()]);
            $errorMessage = sprintf("%s\n%s:%s", $e->getMessage(), $e->getFile(), $e->getLine());

            $subject->setError($errorMessage);
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
