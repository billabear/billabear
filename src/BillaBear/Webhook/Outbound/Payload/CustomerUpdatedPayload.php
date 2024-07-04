<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload;

use BillaBear\Entity\Customer;
use BillaBear\Enum\WebhookEventType;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;

class CustomerUpdatedPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

    public function __construct(private Customer $customer)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::CUSTOMER_UPDATED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::CUSTOMER_UPDATED->value,
            'customer' => $this->getCustomerData($this->customer),
        ];
    }
}
