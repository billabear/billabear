<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Generic;

use BillaBear\Enum\GenericTaskStatus;
use BillaBear\Repository\GenericBackgroundTaskRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class GenericTasks
{
    /**
     * @param ExecutorInterface[]|iterable $executors
     */
    public function __construct(
        #[AutowireIterator('app.background_task.executor')]
        private iterable $executors,
        private GenericBackgroundTaskRepositoryInterface $genericBackgroundTaskRepository,
    ) {
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
