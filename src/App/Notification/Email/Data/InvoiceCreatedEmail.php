<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Notification\Email\Data;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;

class InvoiceCreatedEmail extends AbstractEmailData
{
    public function __construct(private Invoice $invoice, private string $payLink)
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
            'tax_total' => $this->invoice->getTaxTotal(),
            'currency' => $this->invoice->getCurrency(),
            'lines' => array_map([$this, 'getInvoiceLineData'], $this->invoice->getLines()->toArray()),
            'biller_address' => $this->getAddress($this->invoice->getBillerAddress()),
            'payee_address' => $this->getAddress($this->invoice->getPayeeAddress()),
            'pay_link' => $this->payLink,
            'due_date' => $this->invoice->getDueAt()?->format(\DATE_ATOM),
        ];
    }

    private function getInvoiceLineData(InvoiceLine $invoiceLine): array
    {
        return [
            'total' => $invoiceLine->getTotal(),
            'sub_total' => $invoiceLine->getSubTotal(),
            'tax_total' => $invoiceLine->getTaxTotal(),
            'tax_percentage' => $invoiceLine->getTaxPercentage(),
            'description' => $invoiceLine->getDescription(),
        ];
    }
}
