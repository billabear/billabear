<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Invoice;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\AccountingIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;

readonly class SyncInvoice
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function sync(Invoice $invoice)
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            return;
        }

        $integration = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration());
        $invoiceService = $integration->getInvoiceService();
        try {
            if ($invoice->getAccountingReference()) {
                $invoiceService->update($invoice);
            } else {
                $registration = $invoiceService->register($invoice);
                $invoice->setAccountingReference($registration->invoiceReference);
            }
        } catch (\Exception $e) {
            $this->webhookDispatcher->dispatch(new AccountingIntegrationFailure($e));
        }
        $this->invoiceRepository->save($invoice);
    }
}
