<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload;

use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Enum\WebhookEventType;
use BillaBear\Webhook\Outbound\Payload\Parts\PlanPayloadTrait;

class PlanUpdatedPayload implements PayloadInterface
{
    use PlanPayloadTrait;

    public function __construct(private SubscriptionPlan $plan)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::PLAN_UPDATED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::PLAN_UPDATED->value,
            'plan' => $this->createPlanPayload($this->plan),
        ];
    }
}
