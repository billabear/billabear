<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Subscription;

use BillaBear\Entity\Subscription;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\Parts\SubscriptionPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class TrialEndedPayload implements PayloadInterface
{
    use CustomerPayloadTrait;
    use SubscriptionPayloadTrait;

    public function __construct(
        private Subscription $subscription,
    ) {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::TRIAL_ENDED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::TRIAL_ENDED->value,
            'subscription' => $this->getSubscriptionData($this->subscription),
            'customer' => $this->getCustomerData($this->subscription->getCustomer()),
        ];
    }
}
