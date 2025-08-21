<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Payload\Integrations;

use BillaBear\Webhook\Outbound\Payload\Parts\ExceptionPayloadTrait;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookEventType;

class CustomerSupportIntegrationFailure implements PayloadInterface
{
    use ExceptionPayloadTrait;

    public function __construct(private \Exception $exception)
    {
    }

    public function getType(): WebhookEventType
    {
        return WebhookEventType::INTEGRATION_CUSTOMER_SUPPORT_FAILURE;
    }

    public function getPayload(): array
    {
        return [
            'type' => WebhookEventType::INTEGRATION_CUSTOMER_SUPPORT_FAILURE->value,
            'exception' => $this->convertException($this->exception->getPrevious() ?? $this->exception),
        ];
    }
}
