<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data\Workflow;

use BillaBear\Notification\Slack\Data\AbstractNotification;
use BillaBear\Notification\Slack\SlackNotificationEvent;
use BillaBear\Workflow\WorkflowType;

class WorkflowFailure extends AbstractNotification
{
    public function __construct(
        private WorkflowType $workflowType,
        private string $transition,
        private string $errorMessage,
    ) {
    }

    public function getEvent(): SlackNotificationEvent
    {
        return SlackNotificationEvent::WORKFLOW_FAILURE;
    }

    protected function getData(): array
    {
        return [
            'workflow' => $this->workflowType->value,
            'transition' => $this->transition,
            'error_message' => $this->errorMessage,
        ];
    }
}
