<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload;

use BillaBear\Entity\Payment;
use BillaBear\Webhook\Outbound\Payload\Parts\CustomerPayloadTrait;
use BillaBear\Webhook\Outbound\WebhookEventType;

class PaymentReceivedPayload implements PayloadInterface
{
    use CustomerPayloadTrait;

    public function __construct(private Payment $payment)
    {
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::PAYMENT_RECEIVED->value,
            'id' => (string) $this->payment->getId(),
            'customer' => $this->getCustomerData($this->payment->getCustomer()),
            'amount' => $this->payment->getAmount(),
        ];
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::PAYMENT_RECEIVED;
    }
}
