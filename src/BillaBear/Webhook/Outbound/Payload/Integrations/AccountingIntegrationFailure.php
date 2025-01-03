<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Integrations;

use BillaBear\Webhook\Outbound\Payload\Parts\ExceptionPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class AccountingIntegrationFailure implements PayloadInterface
{
    use ExceptionPayloadTrait;

    public function __construct(private \Exception $exception)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::INTEGRATION_ACCOUNTING_FAILURE;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::INTEGRATION_ACCOUNTING_FAILURE->value,
            'exception' => $this->convertException($this->exception->getPrevious() ?? $this->exception),
        ];
    }
}
