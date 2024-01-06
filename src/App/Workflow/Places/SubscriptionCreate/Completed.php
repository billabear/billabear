<?php

namespace App\Workflow\Places\SubscriptionCreate;

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
        return WorkflowType::CREATE_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'complete';
    }
}