<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Repository\RepositoryInterface;

/**
 * @method WorkflowTransition findById($id)
 */
interface WorkflowTransitionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return WorkflowTransition[]
     */
    public function findForWorkflow(WorkflowType $workflowType): array;

    /**
     * @return WorkflowTransition[]
     */
    public function findEnabledForWorkflow(WorkflowType $workflowType): array;

    public function delete(WorkflowTransition $transition): void;
}
