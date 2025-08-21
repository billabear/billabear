<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Repository\DoctrineRepository;

class WorkflowTransitionRepository extends DoctrineRepository implements WorkflowTransitionRepositoryInterface
{
    public function findForWorkflow(WorkflowType $workflowType): array
    {
        return $this->entityRepository->findBy(['workflow' => $workflowType]);
    }

    public function findEnabledForWorkflow(WorkflowType $workflowType): array
    {
        return $this->entityRepository->findBy(['workflow' => $workflowType, 'enabled' => true]);
    }

    public function delete(WorkflowTransition $transition): void
    {
        $this->entityRepository->getEntityManager()->remove($transition);
        $this->entityRepository->getEntityManager()->flush();
    }
}
