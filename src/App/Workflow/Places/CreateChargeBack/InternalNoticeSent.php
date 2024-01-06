<?php

namespace App\Workflow\Places\CreateChargeBack;

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
        return 1200;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_CHARGEBACK;
    }

    public function getToTransitionName(): string
    {
        return 'send_internal_notice';
    }
}