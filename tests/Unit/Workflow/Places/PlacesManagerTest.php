<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Workflow\Places;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;
use App\Workflow\Places\PlacesManager;
use PHPUnit\Framework\TestCase;

class PlacesManagerTest extends TestCase
{
    public function testThatReturnsOnlyPlacesForWorkflow(): void
    {
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
        };

        $subject = new PlacesManager();
        $subject->addPlace($placeOne);
        $subject->addPlace($placeTwo);
        $subject->addPlace($placeThree);

        $places = $subject->getPlacesForWorkflow(WorkflowType::CANCEL_SUBSCRIPTION);
        $this->assertCount(2, $places);
        $this->assertContains($placeOne, $places);
        $this->assertContains($placeTwo, $places);
        $this->assertNotContains($placeThree, $places);

        $places = $subject->getPlacesForWorkflow(WorkflowType::CREATE_SUBSCRIPTION);
        $this->assertCount(1, $places);
        $this->assertNotContains($placeOne, $places);
        $this->assertNotContains($placeTwo, $places);
        $this->assertContains($placeThree, $places);
    }
}
