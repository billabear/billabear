<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Usage;

use BillaBear\Pricing\Usage\Messenger\Message\UpdateCustomerCounters;
use BillaBear\Repository\Usage\EventRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CounterUpdate
{
    use LoggerAwareTrait;

    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function execute(): void
    {
        $this->getLogger()->info('Executing metric counter updater');

        // Make sure we don't lose any and it doesn't matter if we reprocess some.
        $past = new \DateTime('-62 seconds');

        $customerIds = $this->eventRepository->getUniqueCustomerIdsSince($past);

        foreach ($customerIds as $customerId) {
            // Send to another process to process the updating of the counters.
            // This should allow it to scale and process quickly.
            $message = new UpdateCustomerCounters($customerId['customer_id']);
            $this->messageBus->dispatch($message);
        }
    }
}
