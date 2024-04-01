<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
