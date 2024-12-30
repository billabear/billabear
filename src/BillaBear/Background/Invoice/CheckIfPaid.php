<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Invoice;

use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class CheckIfPaid
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    public function execute(): void
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (!$settings->getAccountingIntegration()->getEnabled()) {
            $this->getLogger()->info('Accounting integration is not enabled, skipping check for paid invoices');

            return;
        }

        $this->getLogger()->info('Checking with the accounting integration if invoices are paid');
        $invoiceService = $this->integrationManager->getAccountingIntegration($settings->getAccountingIntegration()->getIntegration())->getInvoiceService();
        $invoices = $this->invoiceRepository->getList();
        $invoices = $invoices->getResults();
        foreach ($invoices as $invoice) {
            $isPaid = $invoiceService->isPaid($invoice);
            if ($isPaid) {
                $this->getLogger()->info('Marking invoice paid via accounting integration', ['invoice_id' => (string) $invoice->getId()]);
                $invoice->setPaid(true);
                $invoice->setUpdatedAt(new \DateTime());
                $this->invoiceRepository->save($invoice);
            }
        }
    }
}
