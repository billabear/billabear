<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Places\PaymentCreated;

use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\WorkflowType;

class CustomerNoticeSent implements PlaceInterface
{
    public function getName(): string
    {
        return 'customer_notice_sent';
    }

    public function getPriority(): int
    {
        return 1200;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_PAYMENT;
    }

    public function getToTransitionName(): string
    {
        return 'send_customer_notice';
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
