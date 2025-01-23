<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Action;

use BillaBear\Entity\Invoice;
use BillaBear\Exception\Integrations\UnexpectedErrorException;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Integrations\AccountingIntegrationFailure;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Common\LoggerAwareTrait;

class SyncInvoice
{
    use LoggerAwareTrait;

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
            if ($e instanceof UnexpectedErrorException) {
                $this->getLogger()->warning('An integration failure happened when syncing invoice', ['invoice_id' => $invoice->getId(), 'integration' => $integration->getName()]);
            } else {
                $this->getLogger()->error('An problem occured happened when syncing invoice', ['invoice_id' => $invoice->getId(), 'integration' => $integration->getName()]);
            }
            $this->webhookDispatcher->dispatch(new AccountingIntegrationFailure($e));
            throw $e;
        }
        $this->invoiceRepository->save($invoice);
    }
}
