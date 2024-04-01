<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Webhook\Outbound\Messenger;

use App\Webhook\Outbound\EventDispatcherInterface;
use App\Webhook\Outbound\Payload\PayloadInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(PayloadInterface $payload): void
    {
        $this->messageBus->dispatch(new EventMessage($payload->getType(), $payload->getPayload()));
    }
}
