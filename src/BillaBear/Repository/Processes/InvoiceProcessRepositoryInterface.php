<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Processes;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\Processes\InvoiceProcess;
use Parthenon\Common\Repository\RepositoryInterface;

interface InvoiceProcessRepositoryInterface extends RepositoryInterface
{
    public function getForInvoice(Invoice $invoice): InvoiceProcess;

    /**
     * @return InvoiceProcess[]
     */
    public function getOverdueBy30days(): array;
}
