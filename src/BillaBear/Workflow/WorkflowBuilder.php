<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Enum\WorkflowType;
use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Workflow\Debug\TraceableWorkflow;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Autoconfigure(lazy: true)]
class WorkflowBuilder
{
    use LoggerAwareTrait;

    /**
     * @param EventDispatcherInterface|EventDispatcher $eventDispatcher
     */
    public function __construct(
        private PlacesProvider $placesProvider,
        private EventDispatcherInterface $eventDispatcher,
        private DynamicTransitionHandlerProvider $dynamicHandlerManager,
        #[Autowire('%kernel.environment%')]
        private string $env,
    ) {
    }

    public function build(WorkflowType $workflowType): WorkflowInterface
    {
        $places = $this->placesProvider->getEnabledPlacesForWorkflow($workflowType);

        if (0 === sizeof($places)) {
            throw new \RuntimeException(sprintf("There are no places for workflow '%s'", $workflowType->value));
        }

        $definition = new Definition(
            $this->getPlaceNames($places),
            $this->getTransitions($places),
            [$this->getPlaceNames($places)[0]],
            new \Symfony\Component\Workflow\Metadata\InMemoryMetadataStore([], [], new \SplObjectStorage())
        );

        $this->addEventHandlers($workflowType, $places);

        $workFlow = new StateMachine(
            $definition,
            new MethodMarkingStore(true, 'state'),
            $this->eventDispatcher,
            $workflowType->value,
            null,
        );

        if ('dev' === $this->env || 'test' === $this->env) {
            return new TraceableWorkflow($workFlow, new Stopwatch());
        }

        return $workFlow;
    }

    private function getPlaceNames(array $places): array
    {
        return array_map(function (PlaceInterface $place) {
            return $place->getName();
        }, $places);
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
            if (!$place->isEnabled()) {
                continue;
            }
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

    /**
     * @param PlaceInterface[] $places
     */
    private function addEventHandlers(WorkflowType $workflowType, array $places): void
    {
        foreach ($places as $place) {
            if ($place instanceof WorkflowTransition) {
                $handler = $this->dynamicHandlerManager->createHandler($place->getHandlerName(), $place);
                $this->eventDispatcher->addListener(sprintf('workflow.%s.transition.%s', $workflowType->value, $place->getToTransitionName()), [$handler, 'execute']);
            }
        }
    }
}
