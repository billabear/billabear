<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\Action;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;

readonly class SyncCustomer
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function sync(Customer $customer): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getNewsletterIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getNewsletterIntegration($settings->getNewsletterIntegration()->getIntegration());

        $customerService = $integration->getCustomerService();
        if ($customer->getNewsletterReference()) {
            $customerService->update($customer);
        } elseif ($customer->getMarketingOptIn()) {
            // Only register them if they've opted in for marketing.
            $registration = $customerService->register($customer);
            $customer->setNewsletterReference($registration->reference);
        }
        $this->customerRepository->save($customer);
    }
}
