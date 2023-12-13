<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;
use App\Workflow\Places\PlacesProvider;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Workflow\Debug\TraceableWorkflow;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WorkflowBuilder
{
    public function __construct(
        private PlacesProvider $placesProvider,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function build(WorkflowType $workflowType): WorkflowInterface
    {
        $places = $this->placesProvider->getPlacesForWorkflow($workflowType);

        if (0 === sizeof($places)) {
            throw new \RuntimeException(sprintf("There are no places for workflow '%s'", $workflowType->value));
        }

        $definition = new Definition(
            $this->getPlaceNames($places),
            $this->getTransitions($places),
            [$this->getPlaceNames($places)[0]],
            new \Symfony\Component\Workflow\Metadata\InMemoryMetadataStore([], [], new \SplObjectStorage())
        );

        $workFlow = new StateMachine(
            $definition,
            new MethodMarkingStore(true, 'state'),
            $this->eventDispatcher,
            $workflowType->value,
            null,
        );

        return new TraceableWorkflow($workFlow, new Stopwatch());
    }

    private function getPlaceNames(array $places): array
    {
        return array_map(function (PlaceInterface $place) { return $place->getName(); }, $places);
    }

    /**
     * @param PlaceInterface[] $places
     *
     * @return Transition[]
     */
    private function getTransitions(array $places): array
    {
        $output = [];
        $from = null;
        foreach ($places as $place) {
            if ($from instanceof PlaceInterface) {
                $output[] = new Transition(
                    $place->getToTransitionName(),
                    $from->getName(),
                    $place->getName(),
                );
            }

            $from = $place;
        }

        return $output;
    }
}
