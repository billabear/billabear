<?php

namespace App\Workflow\Places\SubscriptionCreate;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class InternalNoticeSent implements PlaceInterface
{
    public function getName(): string
    {
        return 'internal_notice_sent';
    }

    public function getPriority(): int
    {
        return 600;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'send_internal_notice';
    }
}