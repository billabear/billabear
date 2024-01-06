<?php

namespace App\Workflow\Places\PaymentCreated;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class ReportInputsGenerated implements PlaceInterface
{
    public function getName(): string
    {
        return 'report_inputs_generated';
    }

    public function getPriority(): int
    {
        return 800;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_PAYMENT;
    }

    public function getToTransitionName(): string
    {
        return 'generate_report_data';
    }
}