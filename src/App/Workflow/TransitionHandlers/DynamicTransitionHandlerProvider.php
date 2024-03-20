<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers;

use App\Entity\WorkflowTransition;
use App\Exception\Workflow\NoHandlerFoundException;

class DynamicTransitionHandlerProvider
{
    /**
     * @var DynamicTransitionHandlerInterface[]
     */
    private array $handlers = [];

    public function addHandler(DynamicTransitionHandlerInterface $dynamicHandler)
    {
        $this->handlers[] = $dynamicHandler;
    }

    public function createHandler(string $name, WorkflowTransition $workflowTransition): DynamicTransitionHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->getName() === $name) {
                return $handler->createCloneWithTransition($workflowTransition);
            }
        }
        throw new NoHandlerFoundException(sprintf("Can't find handler for %s", $name));
    }

    /**
     * @return DynamicTransitionHandlerInterface[]
     */
    public function getAll(): array
    {
        return $this->handlers;
    }
}
