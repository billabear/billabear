<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Workflow\Places\SubscriptionCancel;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class Completed implements PlaceInterface
{
    public function getName(): string
    {
        return 'completed';
    }

    public function getPriority(): int
    {
        return 1200;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CANCEL_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'complete';
    }
}
