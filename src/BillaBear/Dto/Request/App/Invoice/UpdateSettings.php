<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Invoice;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSettings
{
    #[Assert\Choice(choices: ['random', 'subsequential', 'format'])]
    #[Assert\NotBlank(allowNull: true)]
    #[SerializedName('invoice_number_generation')]
    private $invoiceNumberGeneration;

    #[Assert\PositiveOrZero]
    #[SerializedName('subsequential_number')]
    private $subsequentialNumber;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Type('string')]
    #[SerializedName('format')]
    private $format;

    #[Assert\Choice(choices: ['30 days', '60 days', '90 days', '120 days'])]
    #[SerializedName('default_invoice_due_time')]
    private $defaultInvoiceDueTime;

    #[Assert\Choice(choices: ['periodically', 'end_of_month'])]
    #[SerializedName('invoice_generation')]
    private $invoiceGeneration;

    public function getInvoiceNumberGeneration()
    {
        return $this->invoiceNumberGeneration;
    }

    public function setInvoiceNumberGeneration($invoiceNumberGeneration): void
    {
        $this->invoiceNumberGeneration = $invoiceNumberGeneration;
    }

    public function getSubsequentialNumber()
    {
        return $this->subsequentialNumber;
    }

    public function setSubsequentialNumber($subsequentialNumber): void
    {
        $this->subsequentialNumber = $subsequentialNumber;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format): void
    {
        $this->format = $format;
    }

    public function getDefaultInvoiceDueTime()
    {
        return $this->defaultInvoiceDueTime;
    }

    public function setDefaultInvoiceDueTime($defaultInvoiceDueTime): void
    {
        $this->defaultInvoiceDueTime = $defaultInvoiceDueTime;
    }

    public function getInvoiceGeneration()
    {
        return $this->invoiceGeneration;
    }

    public function setInvoiceGeneration($invoiceGeneration): void
    {
        $this->invoiceGeneration = $invoiceGeneration;
    }
}
