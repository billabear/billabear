<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Action;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\CrmIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class SyncCustomer
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function sync(Customer $customer): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getCrmIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getCrmIntegration($settings->getCrmIntegration()->getIntegration());
        $customerService = $integration->getCustomerService();
        try {
            if ($customer->getCrmReference()) {
                $customerService->update($customer);
            } else {
                $customerProfile = $customerService->search($customer);
                if ($customerProfile) {
                    if ($customerProfile->name) {
                        $customer->getBillingAddress()->setCompanyName($customerProfile->name);
                    }

                    if ($customerProfile->city) {
                        $customer->getBillingAddress()->setCity($customerProfile->city);
                    }

                    if ($customerProfile->state) {
                        $customer->getBillingAddress()->setRegion($customerProfile->state);
                    }

                    if ($customerProfile->country) {
                        $customer->getBillingAddress()->setCountry($customerProfile->country);
                    }

                    if ($customerProfile->postCode) {
                        $customer->getBillingAddress()->setPostcode($customerProfile->postCode);
                    }

                    $customer->setCrmReference($customerProfile->reference);

                    if (!$customerProfile->contactReference) {
                        $contactRegistration = $customerService->registerContact($customer);
                        $customer->setCrmContactReference($contactRegistration->reference);
                    } else {
                        $customer->setCrmContactReference($customerProfile->contactReference);
                    }

                    $customerService->update($customer);
                } else {
                    $registration = $customerService->registerCompany($customer);
                    $customer->setCrmReference($registration->reference);
                    $customer->setCrmContactReference($registration->contactReference);
                }
            }
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new CrmIntegrationFailure($e));

            throw $e;
        }
        $this->customerRepository->save($customer);
    }
}
