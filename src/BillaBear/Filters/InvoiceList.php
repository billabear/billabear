<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Filters;

use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;

class InvoiceList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        return [
            'email' => [
                'field' => 'customer.billingEmail',
                'filter' => ContainsFilter::class,
            ],
            'number' => [
                'field' => 'invoiceNumber',
                'filter' => ContainsFilter::class,
            ],
            'customer' => [
                'field' => 'customer.id',
                'filter' => ExactChoiceFilter::class,
            ],
        ];
    }
}
