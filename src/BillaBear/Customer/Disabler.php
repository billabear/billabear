<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Customer\Messenger\CustomerEvent;
use BillaBear\Customer\Messenger\CustomerEventType;
use BillaBear\Entity\Customer;
use BillaBear\Event\Customer\CustomerDisabled;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerDisabledPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class Disabler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private WebhookDispatcherInterface $eventProcessor,
        private EventDispatcherInterface $eventDispatcher,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function disable(Customer $customer): void
    {
        $customer->setStatus(CustomerStatus::DISABLED);
        $this->customerRepository->save($customer);
        $this->eventProcessor->dispatch(new CustomerDisabledPayload($customer));
        $this->eventDispatcher->dispatch(new CustomerDisabled($customer));
        $this->messageBus->dispatch(new CustomerEvent(CustomerEventType::UPDATE, (string) $customer->getId()));
    }
}
