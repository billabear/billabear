<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Repository\CustomerRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncCustomerHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private \BillaBear\Integrations\Accounting\Action\SyncCustomer $syncCustomer,
    ) {
    }

    public function __invoke(SyncCustomer $syncCustomer): void
    {
        $customer = $this->customerRepository->findById($syncCustomer->customerId);
        $this->syncCustomer->sync($customer);
    }
}
