<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Action;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Notification\Email\Email;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\CrmIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

class LogEmail
{
    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function logCustomer(Customer $customer, Email $email): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        if (!$settings->getCrmIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getCrmIntegration($settings->getCrmIntegration()->getIntegration());
        $emailService = $integration->getEmailService();
        try {
            $emailService->registerEmail($customer, $email);
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new CrmIntegrationFailure($e));
            throw $e;
        }
    }
}
