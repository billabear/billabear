<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Workflow\TransitionHandlers;

use App\Entity\WorkflowTransition;
use App\Exception\Workflow\NoHandlerFoundException;
use App\Workflow\TransitionHandlers\DynamicTransitionHandlerInterface;
use App\Workflow\TransitionHandlers\DynamicTransitionHandlerProvider;
use PHPUnit\Framework\TestCase;

class DynamicHandlerProviderTest extends TestCase
{
    public function testReturnsHandler(): void
    {
        $workflowTransition = new WorkflowTransition();
        $handlerOne = $this->createMock(DynamicTransitionHandlerInterface::class);
        $handlerTwo = $this->createMock(DynamicTransitionHandlerInterface::class);

        $handlerOne->method('getName')->willReturn('one');
        $handlerOne->expects($this->once())->method('createCloneWithTransition');
        $handlerTwo->method('getName')->willReturn('two');
        $handlerTwo->expects($this->never())->method('createCloneWithTransition');

        $subject = new DynamicTransitionHandlerProvider();
        $subject->addHandler($handlerOne);
        $subject->addHandler($handlerTwo);
        $subject->createHandler('one', $workflowTransition);
    }

    public function testThrowsExceptionWhenNoHandlerIsFound(): void
    {
        $this->expectException(NoHandlerFoundException::class);

        $workflowTransition = new WorkflowTransition();
        $handlerOne = $this->createMock(DynamicTransitionHandlerInterface::class);
        $handlerTwo = $this->createMock(DynamicTransitionHandlerInterface::class);

        $handlerOne->method('getName')->willReturn('one');
        $handlerTwo->method('getName')->willReturn('two');

        $subject = new DynamicTransitionHandlerProvider();
        $subject->addHandler($handlerOne);
        $subject->addHandler($handlerTwo);

        $subject->createHandler('three', $workflowTransition);
    }
}
