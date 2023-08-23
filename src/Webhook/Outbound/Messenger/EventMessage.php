<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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