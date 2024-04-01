<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository\Processes;

use App\Entity\Processes\InvoiceProcess;
use Parthenon\Common\Repository\RepositoryInterface;

interface InvoiceProcessRepositoryInterface extends RepositoryInterface
{
    public function getForInvoice(\App\Entity\Invoice $invoice): InvoiceProcess;

    /**
     * @return InvoiceProcess[]
     */
    public function getOverdueBy30days(): array;
}
