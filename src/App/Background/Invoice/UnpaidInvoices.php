<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Invoice;

use App\Repository\InvoiceRepositoryInterface;
use App\Repository\Processes\InvoiceProcessRepositoryInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class UnpaidInvoices
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceProcessRepositoryInterface $invoiceProcessRepository,
        private WorkflowInterface $invoiceProcessStateMachine,
    ) {
    }

    public function execute(): void
    {
        $overdueInvoices = $this->invoiceRepository->getOverdueInvoices();

        foreach ($overdueInvoices as $invoice) {
            $process = $this->invoiceProcessRepository->getForInvoice($invoice);
            if ($this->invoiceProcessStateMachine->can($process, 'send_customer_warning')) {
                $this->invoiceProcessStateMachine->apply($process, 'send_customer_warning');
            }
        }
    }
}
