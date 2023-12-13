<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SystemSettings
{
    #[SerializedName('system_url')]
    private ?string $systemUrl = null;

    #[SerializedName('timezone')]
    private ?string $timezone = null;

    #[SerializedName('invoice_number_generation')]
    private ?string $invoiceNumberGeneration = null;

    #[SerializedName('subsequential_number')]
    private ?int $subsequentialNumber = null;

    #[SerializedName('default_invoice_due_time')]
    private ?string $defaultInvoiceDueTime = null;

    public function getSystemUrl(): ?string
    {
        return $this->systemUrl;
    }

    public function setSystemUrl(?string $systemUrl): void
    {
        $this->systemUrl = $systemUrl;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

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
}
