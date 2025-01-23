<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Refund;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\RefundRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\AccountingIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class SyncRefund
{
    public function __construct(
        private RefundRepositoryInterface $refundRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function sync(Refund $refund): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }
        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $customerService = $integration->getCreditService();
        if ($refund->getAccountingReference()) {
            return;
        }

        try {
            $registration = $customerService->registerRefund($refund);
            $refund->setAccountingReference($registration->refundReference);
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new AccountingIntegrationFailure($e));

            throw $e;
        }

        $this->refundRepository->save($refund);
    }
}
