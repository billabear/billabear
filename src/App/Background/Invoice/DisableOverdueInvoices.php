<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Invoice;

use App\Invoice\InvoiceStateMachineProcessor;
use App\Repository\Processes\InvoiceProcessRepositoryInterface;

class DisableOverdueInvoices
{
    public function __construct(
        private InvoiceProcessRepositoryInterface $invoiceProcessRepository,
        private InvoiceStateMachineProcessor $invoiceStateMachineProcessor,
    ) {
    }

    public function execute(): void
    {
        $processes = $this->invoiceProcessRepository->getOverdueBy30days();

        foreach ($processes as $process) {
            $this->invoiceStateMachineProcessor->processDisableCustomer($process);
        }
    }
}
