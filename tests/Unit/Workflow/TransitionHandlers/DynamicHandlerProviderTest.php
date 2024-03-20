<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
