<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Filters\Stats;

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

        if (null !== $request->get('subscription_plan', null)) {
            $output['subscription_plan'] = $request->get('subscription_plan');
        }

        if (null !== $request->get('brand', null)) {
            $output['brand'] = $request->get('brand');
        }

        return $output;
    }
}
