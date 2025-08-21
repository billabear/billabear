<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Filters;

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
            'company_name' => [
                'field' => '.billingAddress.companyName',
                'filter' => ContainsFilter::class,
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
