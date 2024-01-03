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

namespace App\Tests\Unit\Workflow;

use App\Enum\WorkflowType;
use App\Workflow\WorkflowBuilder;
use App\Workflow\WorkflowProcessInterface;
use App\Workflow\WorkflowProcessor;
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

        $transition->method('getName')->willReturn(self::TRANSITION_NAME);

        $definition->method('getTransitions')->willReturn([$transition]);

        $builder->method('build')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')->with($process, self::TRANSITION_NAME)->willReturn(true);
        $workflow->expects($this->once())->method('apply')->with($process, self::TRANSITION_NAME);

        $process->expects($this->once())->method('setHasError')->with(false);

        $repository->expects($this->once())->method('save');

        $subject = new WorkflowProcessor($builder);
        $subject->process($process, WorkflowType::CANCEL_SUBSCRIPTION, $repository);
    }
}
