<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Workflow;

use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Workflow\WorkflowBuilder;
use BillaBear\Workflow\WorkflowProcessInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class WorkFlowProcessorTest extends TestCase
{
    public const TRANSITION_NAME = 'transition_name';

    public function testProcessor(): void
    {
        \DG\BypassFinals::enable();
        $builder = $this->createMock(WorkflowBuilder::class);
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);
        $transition = $this->createMock(Transition::class);
        $notificationSender = $this->createMock(NotificationSender::class);

        $transition->method('getName')->willReturn(self::TRANSITION_NAME);

        $definition->method('getTransitions')->willReturn([$transition]);

        $builder->method('build')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')->with($process, self::TRANSITION_NAME)->willReturn(true);
        $workflow->expects($this->once())->method('apply')->with($process, self::TRANSITION_NAME);

        $process->expects($this->once())->method('setHasError')->with(false);

        $repository->expects($this->once())->method('save');

        $subject = new WorkflowProcessor($builder, $notificationSender);
        $subject->process($process, WorkflowType::CANCEL_SUBSCRIPTION, $repository);
    }
}
