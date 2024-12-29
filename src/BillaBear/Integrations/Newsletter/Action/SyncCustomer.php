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
use Parthenon\Common\LoggerAwareTrait;

class SyncCustomer
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function sync(Customer $customer): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $newsletterSettings = $settings->getNewsletterIntegration();
        if (!$newsletterSettings->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getNewsletterIntegration($newsletterSettings->getIntegration());

        $customerService = $integration->getCustomerService();
        if ($newsletterSettings->getMarketingListId()) {
            if ($customer->getNewsletterMarketingReference()) {
                $customerService->update($newsletterSettings->getMarketingListId(), $customer);
            } elseif ($customer->getMarketingOptIn()) {
                // Only register them if they've opted in for marketing.
                $registration = $customerService->register($newsletterSettings->getMarketingListId(), $customer);
                $customer->setNewsletterMarketingReference($registration->reference);
            }
        }

        if ($newsletterSettings->getAnnouncementListId()) {
            if ($customer->getNewsletterAnnouncementReference()) {
                $customerService->update($newsletterSettings->getAnnouncementListId(), $customer);
            } else {
                // These aren't meant to be marketing emails therefore it's not necessary to check if they've opted in.
                $registration = $customerService->register($newsletterSettings->getAnnouncementListId(), $customer);
                $customer->setNewsletterAnnouncementReference($registration->reference);
            }
        }

        $this->customerRepository->save($customer);
    }
}
