<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy;

use BillaBear\Entity\WebhookEvent;
use BillaBear\Repository\WebhookEventRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\PayloadInterface;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

class WebhookDispatcher implements WebhookDispatcherInterface
{
    public function __construct(private WebhookEventRepositoryInterface $eventRepository)
    {
    }

    public function dispatch(PayloadInterface $payload): void
    {
        $event = new WebhookEvent();
        $event->setType($payload->getType());
        $event->setPayload(json_encode($payload->getPayload()));
        $event->setCreatedAt(new \DateTime());
        $this->eventRepository->save($event);
    }
}
