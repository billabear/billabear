<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Customer;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class InvoiceFormatterProvider
{
    public function __construct(
        #[TaggedIterator('billabear.invoice_formatter')]
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

    public function getFormatterByType(string $format): InvoiceFormatterInterface
    {
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
