<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Customer;

use BillaBear\Entity\Customer;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class CustomerCreatedPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

    public function __construct(private Customer $customer)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::CUSTOMER_CREATED;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::CUSTOMER_CREATED->value,
            'customer' => $this->getCustomerData($this->customer),
        ];
    }
}
