<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Credit;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\CreditRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\AccountingIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class SyncCredit
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function sync(Credit $credit): void
    {
        if (!$credit->isCredit()) {
            return;
        }
        if ($credit->getAccountingReference()) {
            return;
        }

        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $customerService = $integration->getCreditService();
        try {
            $registration = $customerService->registeredCreditNote($credit);
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new AccountingIntegrationFailure($e));

            throw $e;
        }
        $credit->setAccountingReference($registration->creditReference);

        $this->creditRepository->save($credit);
    }
}
