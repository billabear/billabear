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
