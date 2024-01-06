<?php

namespace App\Workflow\Places\SubscriptionCreate;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

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
        return WorkflowType::CREATE_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'send_customer_notice';
    }
}