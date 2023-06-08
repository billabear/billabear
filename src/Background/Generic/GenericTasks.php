<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Background\Generic;

use App\Enum\GenericTaskStatus;
use App\Repository\GenericBackgroundTaskRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class GenericTasks
{
    /**
     * @param ExecutorInterface[]|iterable $executors
     */
    public function __construct(
        #[TaggedIterator('app.background_task.executor')]
        private iterable $executors,
        private GenericBackgroundTaskRepositoryInterface $genericBackgroundTaskRepository,
    ) {
    }

    public function addExecutor(ExecutorInterface $executor): void
    {
        $this->executors[] = $executor;
    }

    public function execute(): void
    {
        $backgroundTasks = $this->genericBackgroundTaskRepository->getNonCompleted();

        foreach ($backgroundTasks as $backgroundTask) {
            $backgroundTask->setStatus(GenericTaskStatus::ACTIVE);
            $backgroundTask->setUpdatedAt(new \DateTime('now'));
            $this->genericBackgroundTaskRepository->save($backgroundTask);

            foreach ($this->executors as $executor) {
                if ($executor->supports($backgroundTask)) {
                    $executor->execute($backgroundTask);
                }
            }

            $backgroundTask->setStatus(GenericTaskStatus::COMPLETED);
            $backgroundTask->setUpdatedAt(new \DateTime('now'));
            $this->genericBackgroundTaskRepository->save($backgroundTask);
        }
    }
}
