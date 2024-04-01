<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Payload;

use App\Entity\Subscription;
use App\Enum\WebhookEventType;

class StartSubscriptionPayload implements PayloadInterface
{
    use CustomerPayloadTrait;
    use SubscriptionPayloadTrait;

    public function __construct(
        private Subscription $subscription,
    ) {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::SUBSCRIPTION_CREATED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::SUBSCRIPTION_CREATED->value,
            'subscription' => $this->getSubscriptionData($this->subscription),
            'customer' => $this->getCustomerData($this->subscription->getCustomer()),
        ];
    }
}
