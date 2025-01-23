<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Invoice;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('billabear.invoice_formatter')]
interface InvoiceFormatterInterface
{
    public function generate(Invoice $invoice): mixed;

    public function filename(Invoice $invoice): string;

    public function name(): string;

    public function supports(string $type): bool;
}
