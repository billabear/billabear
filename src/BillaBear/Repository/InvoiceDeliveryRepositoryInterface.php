<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceDelivery;
use Parthenon\Common\Repository\RepositoryInterface;

interface InvoiceDeliveryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return InvoiceDelivery[]
     */
    public function getForInvoice(Invoice $invoice): array;
}
