<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Customer;

use BillaBear\Entity\Customer;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class CustomerEnabledPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

    public function __construct(private Customer $customer)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::CUSTOMER_ENABLED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::CUSTOMER_ENABLED->value,
            'customer' => $this->getCustomerData($this->customer),
        ];
    }
}
