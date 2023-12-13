<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
