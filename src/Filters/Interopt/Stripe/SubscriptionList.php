<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Filters\Interopt\Stripe;

use App\Filters\AbstractFilterList;
use Parthenon\Athena\Filters\ExactChoiceFilter;
use Parthenon\Athena\Filters\GreaterThanFilter;
use Parthenon\Athena\Filters\GreaterThanOrEqualFilter;
use Parthenon\Athena\Filters\LessThanFilter;
use Parthenon\Athena\Filters\LessThanOrEqualFilter;

class SubscriptionList extends AbstractFilterList
{
    protected function getFilters(): array
    {
        $timestampConverter = function ($value) {
            if (!ctype_digit($value)) {
                throw new \Exception('Invalid data type');
            }

            $dateTime = new \DateTime();
            $dateTime->setTimestamp($value);

            return $dateTime;
        };

        return [
            'customer' => [
                'field' => 'customer',
                'filter' => ExactChoiceFilter::class,
            ],
            'price' => [
                'field' => 'price',
                'filter' => ExactChoiceFilter::class,
            ],
            'created' => [
                'field' => 'createdAt',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[gt]' => [
                'field' => 'createdAt',
                'filter' => GreaterThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[gte]' => [
                'field' => 'createdAt',
                'filter' => GreaterThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[lt]' => [
                'field' => 'createdAt',
                'filter' => LessThanFilter::class,
                'converter' => $timestampConverter,
            ],
            'created[lte]' => [
                'field' => 'createdAt',
                'filter' => LessThanOrEqualFilter::class,
                'converter' => $timestampConverter,
            ],
        ];
    }
}
