<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Workflow;

use App\Entity\WorkflowTransition;
use App\Enum\WorkflowType;
use App\Workflow\Places\PlacesProvider;
use App\Workflow\TransitionHandlers\DynamicHandlerInterface;
use App\Workflow\TransitionHandlers\DynamicHandlerProvider;
use App\Workflow\WorkflowBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WorkflowBuilderTest extends TestCase
{
    public function testThatItCreatesEventHandlersForEntityEvents(): void
    {
        $handlerName = 'cool_handler';
        $placeName = 'transition_a_cool_place';

        $placesProvider = $this->createMock(PlacesProvider::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $dynamicHandlerProvider = $this->createMock(DynamicHandlerProvider::class);
        $handler = $this->createMock(DynamicHandlerInterface::class);
        $placeEntity = $this->createMock(WorkflowTransition::class);

        $placeEntity->method('getHandlerName')->willReturn($handlerName);
        $placeEntity->method('getToTransitionName')->willReturn($placeName);

        $placesProvider->method('getPlacesForWorkflow')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn([$placeEntity]);
        $dynamicHandlerProvider->method('getHandlerByName')->with($handlerName)->willReturn($handler);

        $eventDispatcher->expects($this->once())->method('addListener')->with(sprintf('workflow.%s.transition.%s', WorkflowType::CANCEL_SUBSCRIPTION->value, $placeName), [$handler, 'execute']);

        $subject = new WorkflowBuilder(
            $placesProvider,
            $eventDispatcher,
            $dynamicHandlerProvider,
            'prod'
        );

        $subject->build(WorkflowType::CANCEL_SUBSCRIPTION);
    }
}
