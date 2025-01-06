<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Invoice;

use BillaBear\Dto\Generic\App\Invoice;
use Symfony\Component\Serializer\Attribute\SerializedName;

class ViewInvoice
{
    private Invoice $invoice;

    #[SerializedName('invoice_deliveries')]
    private array $invoiceDeliveries = [];

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getInvoiceDeliveries(): array
    {
        return $this->invoiceDeliveries;
    }

    public function setInvoiceDeliveries(array $invoiceDeliveries): void
    {
        $this->invoiceDeliveries = $invoiceDeliveries;
    }
}
