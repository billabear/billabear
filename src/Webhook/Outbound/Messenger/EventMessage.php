<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Messenger;

use App\Enum\WebhookEventType;
use App\Webhook\Outbound\Payload\PayloadInterface;

class EventMessage implements PayloadInterface
{
    public function __construct(public WebhookEventType $type, private array $payload)
    {
    }

    public function getType(): WebhookEventType
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
