<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Customer;

use App\Entity\Customer;
use App\Enum\CustomerStatus;
use App\Repository\CustomerRepositoryInterface;
use App\Webhook\Outbound\EventDispatcherInterface;
use App\Webhook\Outbound\Payload\CustomerDisabledPayload;

class Disabler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private EventDispatcherInterface $eventProcessor,
    ) {
    }

    public function disable(Customer $customer): void
    {
        $customer->setStatus(CustomerStatus::DISABLED);
        $this->customerRepository->save($customer);
        $this->eventProcessor->dispatch(new CustomerDisabledPayload($customer));
    }
}
