<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Webhook\Outbound\Payload;

use App\Entity\Subscription;
use App\Enum\WebhookEventType;

class StartSubscriptionPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

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
            'subscription' => [
                'id' => (string) $this->subscription->getId(),
                'plan_name' => $this->subscription->getPlanName(),
                'status' => $this->subscription->getStatus()->value,
            ],
            'customer' => $this->getCustomerData($this->subscription->getCustomer()),
        ];
    }
}
