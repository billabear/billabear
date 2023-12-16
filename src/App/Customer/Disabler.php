<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
