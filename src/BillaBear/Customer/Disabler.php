<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Customer;

use BillaBear\Entity\Customer;
use BillaBear\Enum\CustomerStatus;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\CustomerDisabledPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

class Disabler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private WebhookDispatcherInterface $eventProcessor,
    ) {
    }

    public function disable(Customer $customer): void
    {
        $customer->setStatus(CustomerStatus::DISABLED);
        $this->customerRepository->save($customer);
        $this->eventProcessor->dispatch(new CustomerDisabledPayload($customer));
    }
}
