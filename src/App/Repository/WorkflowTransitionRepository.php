<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Enum\WorkflowType;
use Parthenon\Common\Repository\DoctrineRepository;

class WorkflowTransitionRepository extends DoctrineRepository implements WorkflowTransitionRepositoryInterface
{
    public function findForWorkflow(WorkflowType $workflowType): array
    {
        return $this->entityRepository->findBy(['workflow' => $workflowType]);
    }
}
