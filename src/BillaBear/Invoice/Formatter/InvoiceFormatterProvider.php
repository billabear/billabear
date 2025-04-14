<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Customer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class InvoiceFormatterProvider
{
    public function __construct(
        #[AutowireIterator('billabear.invoice_formatter')]
        /**
         * @var iterable<InvoiceFormatterInterface>
         */
        private iterable $formatters,
    ) {
    }

    public function getFormatter(Customer $customer): InvoiceFormatterInterface
    {
        return $this->getFormatterByType($customer->getInvoiceFormat());
    }

    public function getFormatterByType(?string $format): InvoiceFormatterInterface
    {
        $format = $format ?? InvoicePdfGenerator::FORMAT_NAME;

        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($format)) {
                return $formatter;
            }
        }

        throw new \RuntimeException('No formatter found for format '.$format);
    }

    public function getFormattersNames(): array
    {
        $names = [];
        foreach ($this->formatters as $formatter) {
            $names[] = $formatter->name();
        }

        return $names;
    }
}
