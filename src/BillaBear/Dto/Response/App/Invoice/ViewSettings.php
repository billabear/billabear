<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Invoice;

use Symfony\Component\Serializer\Attribute\SerializedName;

class ViewSettings
{
    #[SerializedName('invoice_settings')]
    private InvoiceSettings $invoiceSettings;

    public function getInvoiceSettings(): InvoiceSettings
    {
        return $this->invoiceSettings;
    }

    public function setInvoiceSettings(InvoiceSettings $invoiceSettings): void
    {
        $this->invoiceSettings = $invoiceSettings;
    }
}
