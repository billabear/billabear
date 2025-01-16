<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Workflow\Places\SubscriptionCancel;

use BillaBear\Workflow\Places\PlaceInterface;
use BillaBear\Workflow\WorkflowType;

class SubscriptionCancelled implements PlaceInterface
{
    public function getName(): string
    {
        return 'subscription_cancelled';
    }

    public function getPriority(): int
    {
        return 200;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CANCEL_SUBSCRIPTION;
    }

    public function getToTransitionName(): string
    {
        return 'cancel_subscription';
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
