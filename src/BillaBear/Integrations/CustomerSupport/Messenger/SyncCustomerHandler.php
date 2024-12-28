<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Messenger;

use BillaBear\Integrations\CustomerSupport\Action\SyncCustomer as Action;
use BillaBear\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncCustomerHandler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private Action $syncCustomer,
    ) {
    }

    public function __invoke(SyncCustomer $message)
    {
        $customer = $this->customerRepository->findById($message->customerId);
        $this->syncCustomer->sync($customer);
    }
}
