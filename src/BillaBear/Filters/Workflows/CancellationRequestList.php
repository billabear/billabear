<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Filters\Workflows;

use BillaBear\Filters\AbstractFilterList;
use Parthenon\Athena\Filters\BoolFilter;

class CancellationRequestList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        return [
            'has_error' => [
                'field' => 'hasError',
                'filter' => BoolFilter::class,
            ],
        ];
    }
}
