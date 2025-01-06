<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Invoice;

use BillaBear\Dto\Generic\App\InvoiceDeliverySettings;

class InvoiceDeliveryView
{
    private InvoiceDeliverySettings $settings;

    private array $formatters = [];

    public function getFormatters(): array
    {
        return $this->formatters;
    }

    public function setFormatters(array $formatters): void
    {
        $this->formatters = $formatters;
    }

    public function getSettings(): InvoiceDeliverySettings
    {
        return $this->settings;
    }

    public function setSettings(InvoiceDeliverySettings $settings): void
    {
        $this->settings = $settings;
    }
}
