<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Repository\InvoiceRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncInvoiceHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private \BillaBear\Integrations\Accounting\Action\SyncInvoice $syncInvoice,
    ) {
    }

    public function __invoke(SyncInvoice $syncInvoice): void
    {
        $invoice = $this->invoiceRepository->findById($syncInvoice->invoiceId);
        $this->syncInvoice->sync($invoice);
    }
}
