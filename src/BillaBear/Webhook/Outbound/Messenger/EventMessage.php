<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Messenger;

use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

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
