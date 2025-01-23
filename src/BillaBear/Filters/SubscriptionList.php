<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Filters;

use Parthenon\Athena\Filters\ExactChoiceFilter;

class SubscriptionList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        return [
            'status' => [
                'field' => 'status',
                'filter' => ExactChoiceFilter::class,
            ],
            'customer' => [
                'field' => 'customer.id',
                'filter' => ExactChoiceFilter::class,
            ],
        ];
    }
}
