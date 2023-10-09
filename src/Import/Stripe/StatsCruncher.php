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

namespace App\Import\Stripe;

use App\Stats\CreateSubscriptionCountStats;
use App\Stats\CustomerCreationStats;
use App\Stats\RevenueEstimatesGeneration;

class StatsCruncher
{
    public function __construct(
        private RevenueEstimatesGeneration $estimatesGeneration,
        private CreateSubscriptionCountStats $subscriptionCountStats,
        private CustomerCreationStats $customerCreationStats,
    ) {
    }

    public function execute()
    {
        $this->estimatesGeneration->generate();
        $this->subscriptionCountStats->generate();
        $this->customerCreationStats->generate();
    }
}
