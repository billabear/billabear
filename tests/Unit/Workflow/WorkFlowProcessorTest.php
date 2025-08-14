<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Workflow;

use BillaBear\Notification\Slack\Data\Workflow\WorkflowFailure;
use BillaBear\Notification\Slack\NotificationSender;
use BillaBear\Workflow\WorkflowBuilder;
use BillaBear\Workflow\WorkflowProcessInterface;
use BillaBear\Workflow\WorkflowProcessor;
use BillaBear\Workflow\WorkflowType;
use Parthenon\Common\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class WorkFlowProcessorTest extends TestCase
{
    public const TRANSITION_NAME = 'transition_name';
    public const SECOND_TRANSITION_NAME = 'second_transition';

    private WorkflowBuilder $workflowBuilder;
    private NotificationSender $notificationSender;
    private LoggerInterface $logger;
    private WorkflowProcessor $workflowProcessor;

    protected function setUp(): void
    {
        \DG\BypassFinals::enable();

        $this->workflowBuilder = $this->createMock(WorkflowBuilder::class);
        $this->notificationSender = $this->createMock(NotificationSender::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->workflowProcessor = new WorkflowProcessor($this->workflowBuilder, $this->notificationSender);
        $this->workflowProcessor->setLogger($this->logger);
    }

    public function testProcessSuccessfulWorkflowWithSingleTransition(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);
        $transition = $this->createMock(Transition::class);

        $transition->method('getName')->willReturn(self::TRANSITION_NAME);
        $definition->method('getTransitions')->willReturn([$transition]);

        $this->workflowBuilder->method('build')->with(WorkflowType::CANCEL_SUBSCRIPTION)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')->with($process, self::TRANSITION_NAME)->willReturn(true);
        $workflow->expects($this->once())->method('apply')->with($process, self::TRANSITION_NAME);

        $process->expects($this->once())->method('setHasError')->with(false);
        $process->expects($this->once())->method('setError')->with(null);

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Did transition for workflow', ['workflow' => 'cancel_subscription', 'transition' => self::TRANSITION_NAME]);

        $result = $this->workflowProcessor->process($process, WorkflowType::CANCEL_SUBSCRIPTION, $repository);

        $this->assertSame($process, $result);
    }

    public function testProcessSuccessfulWorkflowWithMultipleTransitions(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);

        $transition1 = $this->createMock(Transition::class);
        $transition2 = $this->createMock(Transition::class);

        $transition1->method('getName')->willReturn(self::TRANSITION_NAME);
        $transition2->method('getName')->willReturn(self::SECOND_TRANSITION_NAME);
        $definition->method('getTransitions')->willReturn([$transition1, $transition2]);

        $this->workflowBuilder->method('build')->with(WorkflowType::CREATE_PAYMENT)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')
            ->willReturnMap([
                [$process, self::TRANSITION_NAME, true],
                [$process, self::SECOND_TRANSITION_NAME, true],
            ]);

        $workflow->expects($this->exactly(2))->method('apply')
            ->with($this->logicalOr($process), $this->logicalOr(self::TRANSITION_NAME, self::SECOND_TRANSITION_NAME));

        $process->expects($this->once())->method('setHasError')->with(false);
        $process->expects($this->once())->method('setError')->with(null);

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->with('Did transition for workflow', $this->logicalOr(
                ['workflow' => 'create_payment', 'transition' => self::TRANSITION_NAME],
                ['workflow' => 'create_payment', 'transition' => self::SECOND_TRANSITION_NAME]
            ));

        $result = $this->workflowProcessor->process($process, WorkflowType::CREATE_PAYMENT, $repository);

        $this->assertSame($process, $result);
    }

    public function testProcessWithTransitionThatCannotBeApplied(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);
        $transition = $this->createMock(Transition::class);

        $transition->method('getName')->willReturn(self::TRANSITION_NAME);
        $definition->method('getTransitions')->willReturn([$transition]);

        $this->workflowBuilder->method('build')->with(WorkflowType::TRIAL_STARTED)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')->with($process, self::TRANSITION_NAME)->willReturn(false);
        $workflow->expects($this->never())->method('apply');

        $process->expects($this->once())->method('setHasError')->with(false);
        $process->expects($this->once())->method('setError')->with(null);

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->once())
            ->method('info')
            ->with("Can't do transition for workflow", ['workflow' => 'trial_started', 'transition' => self::TRANSITION_NAME]);

        $result = $this->workflowProcessor->process($process, WorkflowType::TRIAL_STARTED, $repository);

        $this->assertSame($process, $result);
    }

    public function testProcessWithMixedTransitions(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);

        $transition1 = $this->createMock(Transition::class);
        $transition2 = $this->createMock(Transition::class);

        $transition1->method('getName')->willReturn(self::TRANSITION_NAME);
        $transition2->method('getName')->willReturn(self::SECOND_TRANSITION_NAME);
        $definition->method('getTransitions')->willReturn([$transition1, $transition2]);

        $this->workflowBuilder->method('build')->with(WorkflowType::TRIAL_ENDED)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')
            ->willReturnMap([
                [$process, self::TRANSITION_NAME, true],
                [$process, self::SECOND_TRANSITION_NAME, false],
            ]);

        $workflow->expects($this->once())->method('apply')->with($process, self::TRANSITION_NAME);

        $process->expects($this->once())->method('setHasError')->with(false);
        $process->expects($this->once())->method('setError')->with(null);

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->with($this->logicalOr(
                'Did transition for workflow',
                "Can't do transition for workflow"
            ), $this->logicalOr(
                ['workflow' => 'trial_ended', 'transition' => self::TRANSITION_NAME],
                ['workflow' => 'trial_ended', 'transition' => self::SECOND_TRANSITION_NAME]
            ));

        $result = $this->workflowProcessor->process($process, WorkflowType::TRIAL_ENDED, $repository);

        $this->assertSame($process, $result);
    }

    public function testProcessWithExceptionDuringTransition(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);
        $transition = $this->createMock(Transition::class);

        $exception = new \RuntimeException('Transition failed');
        $expectedErrorMessage = sprintf("%s\n%s:%s", $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $transition->method('getName')->willReturn(self::TRANSITION_NAME);
        $definition->method('getTransitions')->willReturn([$transition]);

        $this->workflowBuilder->method('build')->with(WorkflowType::CREATE_REFUND)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->method('can')->with($process, self::TRANSITION_NAME)->willReturn(true);
        $workflow->method('apply')->with($process, self::TRANSITION_NAME)->willThrowException($exception);

        $process->expects($this->exactly(2))->method('setHasError')
            ->with($this->logicalOr(false, true));
        $process->expects($this->exactly(2))->method('setError')
            ->with($this->logicalOr(null, $expectedErrorMessage));

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with('Transition for workflow failed', [
                'workflow' => 'create_refund',
                'transition' => self::TRANSITION_NAME,
                'message' => 'Transition failed',
            ]);

        $this->notificationSender->expects($this->once())
            ->method('sendNotification')
            ->with($this->isInstanceOf(WorkflowFailure::class));

        $result = $this->workflowProcessor->process($process, WorkflowType::CREATE_REFUND, $repository);

        $this->assertSame($process, $result);
    }

    public function testProcessWithExceptionBeforeAnyTransition(): void
    {
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);

        $exception = new \InvalidArgumentException('Workflow build failed');

        $this->workflowBuilder->method('build')->with(WorkflowType::CREATE_CHARGEBACK)->willThrowException($exception);

        // The exception should be thrown and not caught since workflow builder call is outside try-catch
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Workflow build failed');

        $this->workflowProcessor->process($process, WorkflowType::CREATE_CHARGEBACK, $repository);
    }

    public function testProcessWithNoTransitions(): void
    {
        $workflow = $this->createMock(WorkflowInterface::class);
        $definition = $this->createMock(Definition::class);
        $process = $this->createMock(WorkflowProcessInterface::class);
        $repository = $this->createMock(RepositoryInterface::class);

        $definition->method('getTransitions')->willReturn([]);

        $this->workflowBuilder->method('build')->with(WorkflowType::TRIAL_CONVERTED)->willReturn($workflow);

        $workflow->method('getDefinition')->willReturn($definition);
        $workflow->expects($this->never())->method('can');
        $workflow->expects($this->never())->method('apply');

        $process->expects($this->once())->method('setHasError')->with(false);
        $process->expects($this->once())->method('setError')->with(null);

        $repository->expects($this->once())->method('save')->with($process);

        $this->logger->expects($this->never())->method('info');

        $result = $this->workflowProcessor->process($process, WorkflowType::TRIAL_CONVERTED, $repository);

        $this->assertSame($process, $result);
    }
}
