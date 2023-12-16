<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
