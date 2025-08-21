<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\Messenger;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Newsletter\Action\Setup;
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
        $this->getLogger()->info('Enabling a new newsletter integration');

        $this->customerRepository->wipeNewsletterReferences();

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
