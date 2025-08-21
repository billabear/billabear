<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Places\CreateRefund;

use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\WorkflowType;

class IntegrationSync implements PlaceInterface
{
    public function getName(): string
    {
        return 'integration_sync';
    }

    public function getPriority(): int
    {
        return 1600;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_REFUND;
    }

    public function getToTransitionName(): string
    {
        return 'sync_with_integration';
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
