<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\TransitionHandlers;

use App\Entity\WorkflowTransition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Workflow\Event\Event;

#[AutoconfigureTag('app.workflow.handler')]
interface DynamicTransitionHandlerInterface
{
    public function getName(): string;

    public function getOptions(): array;

    /**
     * On the case of an error where you want to mark the transition as failed throw an instance of
     * \Throwable this will automatically be used to mark it as failed and provide error context via
     * the Admin UI.
     *
     * @throws \Throwable
     */
    public function execute(Event $event): void;

    /**
     * Added to allow the handler to have the transition to get the handler options. Otherwise,
     * the only other option is to fetch the workflow transition in the workflow processor, and
     * it makes no sense to fetch the data in two different places. And this allows more overall
     * flexibility since they'll have access to all the data when executing the handler.
     */
    public function createCloneWithTransition(WorkflowTransition $transition): DynamicTransitionHandlerInterface;
}
