<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Workflow\Places;

use BillaBear\Entity\WorkflowTransition;
use BillaBear\Enum\WorkflowType;
use BillaBear\Repository\WorkflowTransitionRepositoryInterface;
use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\Places\PlacesProvider;
use PHPUnit\Framework\TestCase;

class PlacesProviderTest extends TestCase
{
    public function testThatReturnsOnlyPlacesForWorkflow(): void
    {
        $repository = $this->createMock(WorkflowTransitionRepositoryInterface::class);
        $repository->expects($this->atLeastOnce())->method('findEnabledForWorkflow')->with($this->anything())->willReturn([]);

        $placeOne = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_one';
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
                return 'demo_transition';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $placeTwo = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_two';
            }

            public function getPriority(): int
            {
                return 150;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return 'demo_transition_two';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };
        $placeThree = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_three';
            }

            public function getPriority(): int
            {
                return 150;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CREATE_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return 'demo_transition_two';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $subject = new PlacesProvider($repository);
        $subject->addPlace($placeOne);
        $subject->addPlace($placeTwo);
        $subject->addPlace($placeThree);

        $places = $subject->getEnabledPlacesForWorkflow(WorkflowType::CANCEL_SUBSCRIPTION);
        $this->assertCount(2, $places);
        $this->assertContains($placeOne, $places);
        $this->assertContains($placeTwo, $places);
        $this->assertNotContains($placeThree, $places);

        $places = $subject->getEnabledPlacesForWorkflow(WorkflowType::CREATE_SUBSCRIPTION);
        $this->assertCount(1, $places);
        $this->assertNotContains($placeOne, $places);
        $this->assertNotContains($placeTwo, $places);
        $this->assertContains($placeThree, $places);
    }

    public function testThatReturnsCorrectlySorted(): void
    {
        $entity = new WorkflowTransition();
        $entity->setPriority(125);

        $repository = $this->createMock(WorkflowTransitionRepositoryInterface::class);
        $repository->expects($this->atLeastOnce())->method('findEnabledForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$entity]);

        $placeOne = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_one';
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
                return 'demo_transition';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };
        $placeTwo = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_two';
            }

            public function getPriority(): int
            {
                return 150;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CANCEL_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return 'demo_transition_two';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };
        $placeThree = new class() implements PlaceInterface {
            public function getName(): string
            {
                return 'place_three';
            }

            public function getPriority(): int
            {
                return 150;
            }

            public function getWorkflow(): WorkflowType
            {
                return WorkflowType::CREATE_SUBSCRIPTION;
            }

            public function getToTransitionName(): string
            {
                return 'demo_transition_two';
            }

            public function isEnabled(): bool
            {
                return true;
            }
        };

        $subject = new PlacesProvider($repository);
        $subject->addPlace($placeOne);
        $subject->addPlace($placeTwo);
        $subject->addPlace($placeThree);

        $places = $subject->getEnabledPlacesForWorkflow(WorkflowType::CANCEL_SUBSCRIPTION);
        $this->assertCount(3, $places);
        $this->assertEquals($placeOne, $places[0]);
        $this->assertEquals($entity, $places[1]);
        $this->assertEquals($placeTwo, $places[2]);
        $this->assertNotContains($placeThree, $places);
    }
}
