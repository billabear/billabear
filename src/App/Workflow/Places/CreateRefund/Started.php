<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Workflow\Places\CreateRefund;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class Started implements PlaceInterface
{
    public function getName(): string
    {
        return 'started';
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_REFUND;
    }

    public function getToTransitionName(): string
    {
        return 'started';
    }
}
