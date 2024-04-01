<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Payload;

use App\Entity\Payment;
use App\Enum\WebhookEventType;

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
