<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
