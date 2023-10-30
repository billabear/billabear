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

namespace App\Api\Filters\Stats;

use Symfony\Component\HttpFoundation\Request;

class LifetimeValue
{
    public function getFilters(Request $request): array
    {
        $output = [];

        if (null !== $request->get('country', null)) {
            $output['country'] = $request->get('country');
        }

        if (null !== $request->get('payment_schedule', null)) {
            $output['payment_schedule'] = $request->get('payment_schedule');
        }

        return $output;
    }
}
