<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\Stats\CreateSubscriptionCountStats;
use BillaBear\Stats\CustomerCreationStats;
use BillaBear\Stats\RevenueEstimatesGeneration;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
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
