<?php

namespace App\Workflow\Places\PaymentCreated;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class ReceiptGenerated implements PlaceInterface
{

    public function getName(): string
    {
        return 'receipt_generated';
    }

    public function getPriority(): int
    {
        return 400;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_PAYMENT;
    }

    public function getToTransitionName(): string
    {
        return 'create_receipt';
    }
}