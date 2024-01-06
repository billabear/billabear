<?php

namespace App\Workflow\Places\CreateChargeBack;

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
        return 9999;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_CHARGEBACK;
    }

    public function getToTransitionName(): string
    {
        return 'completed';
    }
}