<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Subscription;

use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Webhook\Outbound\Payload\Parts\PlanPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class PlanCreatedPayload implements PayloadInterface
{
    use PlanPayloadTrait;

    public function __construct(private SubscriptionPlan $plan)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::PLAN_CREATED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::PLAN_CREATED->value,
            'plan' => $this->createPlanPayload($this->plan),
        ];
    }
}
