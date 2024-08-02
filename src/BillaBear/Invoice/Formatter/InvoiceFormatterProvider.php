<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Customer;
use BillaBear\Enum\InvoiceFormat;

class InvoiceFormatterProvider
{
    public function __construct(
        private InvoicePdfGenerator $invoicePdfGenerator,
        private ZUGFeRDFormatter $zugFeRDFormatter,
    ) {
    }

    public function getFormatter(Customer $customer): InvoiceFormatterInterface
    {
        return $this->getFormatterByType($customer->getInvoiceFormat());
    }

    public function getFormatterByType(InvoiceFormat $format): InvoiceFormatterInterface
    {
        $generator = match ($format) {
            InvoiceFormat::ZUGFERD_V1 => $this->zugFeRDFormatter,
            default => $this->invoicePdfGenerator,
        };

        return $generator;
    }
}
