<?php

namespace App\Workflow\Places\CreateChargeBack;

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
        return WorkflowType::CREATE_CHARGEBACK;
    }

    public function getToTransitionName(): string
    {
        return 'started';
    }
}