<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Action;

use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\CrmIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

class Setup
{
    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function setup(): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        if (!$settings->getCrmIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getCustomerSupportIntegration($settings->getCustomerSupportIntegration()->getIntegration());
        try {
            $integration->setup();
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new CrmIntegrationFailure($e));

            throw $e;
        }
    }
}
