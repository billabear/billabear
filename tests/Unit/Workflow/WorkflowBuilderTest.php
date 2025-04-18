<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Workflow;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\Places\PlacesProvider;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerInterface;
use BillaBear\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use BillaBear\Workflow\WorkflowBuilder;
use BillaBear\Workflow\WorkflowType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Debug\TraceableWorkflow;

class WorkflowBuilderTest extends TestCase
{
    public function testThatItCreatesEventHandlersForEntityEvents(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);
        $handler = $this->createMock(DynamicTransitionHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($placeName);
        $placeEntity->method('isEnabled')->willReturn(true);

        $placesProvider->method('getEnabledPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity]);
        $dynamicHandlerProvider->method('createHandler')->with($handlerName)->willReturn($handler);

        $eventDispatcher->expects($this->once())->method('addListener')->with(sprintf('workflow.%s.transition.%s', WorkflowType::CANCEL_SUBSCRIPTION->value, $placeName), [$handler, 'execute']);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );
        $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
    }

    public function testThatWorkflowHasAllThePlaces(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'a_cool_place';
        $transitionName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);
        $handler = $this->createMock(DynamicTransitionHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($transitionName);
        $placeEntity->method('getName')->willReturn($placeName);
        $placeEntity->method('isEnabled')->willReturn(true);

        $placeClassName = 'hardcoded_place';
        $placeClassTransition = 'hardcoded_transition';

        $placeClass = new class($placeClassName, $placeClassTransition) implements PlaceInterface {
            public function __construct(private string $placeClassName, private string $placeClassTransition)
            {
            }

            public function getName(): string
            {
                return $this->placeClassName;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return $this->placeClassTransition;
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $placesProvider->method('getEnabledPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity, $placeClass]);
        $dynamicHandlerProvider->method('createHandler')->with($handlerName)->willReturn($handler);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );
        $workflow = $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
        $definition = $workflow->getDefinition();

        $this->assertCount(2, $definition->getPlaces());
        $this->assertContains($placeClassName, $definition->getPlaces());
        $this->assertContains($placeName, $definition->getPlaces());
    }

    public function testThatWorkflowHasAllTheTransition(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'a_cool_place';
        $transitionName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);
        $handler = $this->createMock(DynamicTransitionHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($transitionName);
        $placeEntity->method('getName')->willReturn($placeName);
        $placeEntity->method('isEnabled')->willReturn(true);

        $placeClassName = 'hardcoded_place';
        $placeClassTransition = 'hardcoded_transition';

        $placeClass = new class($placeClassName, $placeClassTransition) implements PlaceInterface {
            public function __construct(private string $placeClassName, private string $placeClassTransition)
            {
            }

            public function getName(): string
            {
                return $this->placeClassName;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return $this->placeClassTransition;
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $placesProvider->method('getEnabledPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity, $placeClass]);
        $dynamicHandlerProvider->method('createHandler')->with($handlerName)->willReturn($handler);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );
        $workflow = $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
        $definition = $workflow->getDefinition();

        $this->assertCount(1, $definition->getTransitions());
        $transition = $definition->getTransitions()[0];
        $this->assertEquals([$placeName], $transition->getFroms());
        $this->assertEquals([$placeClassName], $transition->getTos());
        $this->assertEquals($placeClassTransition, $transition->getName());
    }

    public function testThatItReturnsATracableWorkflowIfTest(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'a_cool_place';
        $transitionName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);
        $handler = $this->createMock(DynamicTransitionHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($transitionName);
        $placeEntity->method('getName')->willReturn($placeName);
        $placeEntity->method('isEnabled')->willReturn(true);

        $placeClassName = 'hardcoded_place';
        $placeClassTransition = 'hardcoded_transition';

        $placeClass = new class($placeClassName, $placeClassTransition) implements PlaceInterface {
            public function __construct(private string $placeClassName, private string $placeClassTransition)
            {
            }

            public function getName(): string
            {
                return $this->placeClassName;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return $this->placeClassTransition;
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $placesProvider->method('getEnabledPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity, $placeClass]);
        $dynamicHandlerProvider->method('createHandler')->with($handlerName)->willReturn($handler);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'test'
        );
        $workflow = $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
        $this->assertInstanceOf(TraceableWorkflow::class, $workflow);
    }

    public function testThatItDoesNotReturnsATracableWorkflowIfProd(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'a_cool_place';
        $transitionName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);
        $handler = $this->createMock(DynamicTransitionHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($transitionName);
        $placeEntity->method('getName')->willReturn($placeName);
        $placeEntity->method('isEnabled')->willReturn(true);

        $placeClassName = 'hardcoded_place';
        $placeClassTransition = 'hardcoded_transition';

        $placeClass = new class($placeClassName, $placeClassTransition) implements PlaceInterface {
            public function __construct(private string $placeClassName, private string $placeClassTransition)
            {
            }

            public function getName(): string
            {
                return $this->placeClassName;
            }

            public function getPriority(): int
            {
                return 100;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return $this->placeClassTransition;
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $placesProvider->method('getEnabledPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity, $placeClass]);
        $dynamicHandlerProvider->method('createHandler')->with($handlerName)->willReturn($handler);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );
        $workflow = $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
        $this->assertNotInstanceOf(TraceableWorkflow::class, $workflow);
    }

    public function testThrowsExceptionWhenNoPlaces(): void
    {
        $this->expectException(\RuntimeException::class);

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicTransitionHandlerProvider::class);

        $placesProvider->method('getPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([]);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );
        $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
    }
}
