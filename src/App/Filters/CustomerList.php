<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Filters;

use Parthenon\Athena\Filters\ContainsFilter;
use Parthenon\Athena\Filters\ExactChoiceFilter;

class CustomerList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        return [
            'email' => [
                'field' => 'billingEmail',
                'filter' => ContainsFilter::class,
            ],
            'country' => [
                'field' => '.billingAddress.country',
                'filter' => ExactChoiceFilter::class,
            ],
            'reference' => [
                'field' => 'reference',
                'filter' => ContainsFilter::class,
            ],
            'external_reference' => [
                'field' => 'externalCustomerReference',
                'filter' => ContainsFilter::class,
            ],
        ];
    }
}
