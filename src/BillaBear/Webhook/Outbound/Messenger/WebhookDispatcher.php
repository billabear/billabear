<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Messenger;

use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class WebhookDispatcher implements WebhookDispatcherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(PayloadInterface $payload): void
    {
        $this->messageBus->dispatch(new EventMessage($payload->getType(), $payload->getPayload()));
    }
}
