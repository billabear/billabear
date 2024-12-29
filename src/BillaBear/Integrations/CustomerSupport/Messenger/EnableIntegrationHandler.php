<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Messenger;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\CustomerSupport\Action\Setup;
use BillaBear\Repository\CustomerRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class EnableIntegrationHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private Setup $setup,
        private CustomerRepositoryInterface $customerRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(EnableIntegration $enableIntegration): void
    {
        $this->setup->setup();
        if (!$enableIntegration->newIntegration) {
            return;
        }
        $this->getLogger()->info('Enabling a new customer support integration');

        $this->customerRepository->wipeCustomerSupportReferences();

        $lastId = null;
        do {
            $resultList = $this->customerRepository->getList([], lastId: $lastId);
            $lastId = $resultList->getLastKey();

            /** @var Customer $customer */
            foreach ($resultList->getResults() as $customer) {
                $this->messageBus->dispatch(new SyncCustomer((string) $customer->getId()));
            }
        } while ($resultList->hasMore());
    }
}
