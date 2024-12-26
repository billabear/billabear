<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncCustomerHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function __invoke(SyncCustomer $syncCustomer): void
    {
        $customer = $this->customerRepository->findById($syncCustomer->customerId);
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $customerService = $integration->getCustomerService();
        if ($customer->getAccountingReference()) {
            $customerService->update($customer);
        } else {
            $registration = $customerService->register($customer);
            $customer->setAccountingReference($registration->reference);
        }
        $this->customerRepository->save($customer);
    }
}
