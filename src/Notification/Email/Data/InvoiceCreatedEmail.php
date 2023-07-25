<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;

class InvoiceCreatedEmail extends AbstractEmailData
{
    public function __construct(private Invoice $invoice)
    {
    }

    public function getTemplateName(): string
    {
        return EmailTemplate::NAME_INVOICE_CREATED;
    }

    public function getData(Customer $customer, BrandSettings $brandSettings): array
    {
        return [
            'brand' => $this->getBrandData($brandSettings),
            'customer' => $this->getCustomerData($customer),
            'invoice' => $this->getInvoiceData(),
        ];
    }

    private function getInvoiceData(): array
    {
        return [
            'total' => $this->invoice->getTotal(),
            'sub_total' => $this->invoice->getSubTotal(),
            'vat_total' => $this->invoice->getTaxTotal(),
            'currency' => $this->invoice->getCurrency(),
            'lines' => array_map([$this, 'getInvoiceLineData'], $this->invoice->getLines()->toArray()),
            'biller_address' => $this->getAddress($this->invoice->getBillerAddress()),
            'payee_address' => $this->getAddress($this->invoice->getPayeeAddress()),
        ];
    }

    private function getInvoiceLineData(InvoiceLine $invoiceLine): array
    {
        return [
            'total' => $invoiceLine->getTotal(),
            'sub_total' => $invoiceLine->getSubTotal(),
            'vat_total' => $invoiceLine->getTaxTotal(),
            'vat_percentage' => $invoiceLine->getTaxPercentage(),
            'description' => $invoiceLine->getDescription(),
        ];
    }
}
