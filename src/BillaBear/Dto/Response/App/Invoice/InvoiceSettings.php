<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Invoice;

use Symfony\Component\Serializer\Annotation\SerializedName;

class InvoiceSettings
{
    #[SerializedName('invoice_number_generation')]
    private ?string $invoiceNumberGeneration = null;

    #[SerializedName('subsequential_number')]
    private ?int $subsequentialNumber = null;

    #[SerializedName('default_invoice_due_time')]
    private ?string $defaultInvoiceDueTime = null;

    #[SerializedName('format')]
    private ?string $format = null;

    #[SerializedName('invoice_generation')]
    private string $invoiceGeneration;

    public function getInvoiceNumberGeneration(): ?string
    {
        return $this->invoiceNumberGeneration;
    }

    public function setInvoiceNumberGeneration(?string $invoiceNumberGeneration): void
    {
        $this->invoiceNumberGeneration = $invoiceNumberGeneration;
    }

    public function getSubsequentialNumber(): ?int
    {
        return $this->subsequentialNumber;
    }

    public function setSubsequentialNumber(?int $subsequentialNumber): void
    {
        $this->subsequentialNumber = $subsequentialNumber;
    }

    public function getDefaultInvoiceDueTime(): ?string
    {
        return $this->defaultInvoiceDueTime;
    }

    public function setDefaultInvoiceDueTime(?string $defaultInvoiceDueTime): void
    {
        $this->defaultInvoiceDueTime = $defaultInvoiceDueTime;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getInvoiceGeneration(): string
    {
        return $this->invoiceGeneration;
    }

    public function setInvoiceGeneration(string $invoiceGeneration): void
    {
        $this->invoiceGeneration = $invoiceGeneration;
    }
}
