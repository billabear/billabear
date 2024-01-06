<?php

namespace App\Workflow\Places\SubscriptionCreate;

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
        return 200;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'handle_stats';
    }
}