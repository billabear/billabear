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
