<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Outbound\Messenger;

use BillaBear\Webhook\Outbound\EventProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EventHandler
{
    public function __construct(private EventProcessor $eventProcessor)
    {
    }

    public function __invoke(EventMessage $eventMessage)
    {
        $this->eventProcessor->process($eventMessage);
    }
}
