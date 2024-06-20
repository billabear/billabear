<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Places\TrialEnded;

use BillaBear\Enum\WorkflowType;
use BillaBear\Workflow\Places\PlaceInterface;

class CustomerNoticeSent implements PlaceInterface
{
    public function getName(): string
    {
        return 'customer_notice_sent';
    }

    public function getPriority(): int
    {
        return 400;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::TRIAL_ENDED;
    }

    public function getToTransitionName(): string
    {
        return 'send_customer_notice';
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
