<?php

namespace App\Workflow\Places\PaymentCreated;

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
        return WorkflowType::CREATE_PAYMENT;
    }

    public function getToTransitionName(): string
    {
        return 'started';
    }
}