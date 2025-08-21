<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Invoice;

use BillaBear\Invoice\InvoiceStateMachineProcessor;
use BillaBear\Repository\Processes\InvoiceProcessRepositoryInterface;

readonly class DisableOverdueInvoices
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
