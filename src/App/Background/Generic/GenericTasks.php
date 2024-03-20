<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
