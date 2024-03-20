<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 */

namespace App\Workflow\Places\CreateChargeBack;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class StatsGenerated implements PlaceInterface
{
    public function getName(): string
    {
        return 'stats_generated';
    }

    public function getPriority(): int
    {
        return 400;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_CHARGEBACK;
    }

    public function getToTransitionName(): string
    {
        return 'handle_stats';
    }
}
