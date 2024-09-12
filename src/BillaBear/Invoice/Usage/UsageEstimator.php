<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Usage;

use BillaBear\Entity\Subscription;
use BillaBear\Invoice\Pricer;
use BillaBear\Repository\Usage\MetricCounterRepositoryInterface;

class UsageEstimator
{
    public function __construct(
        private MetricProvider $metricProvider,
        private Pricer $pricer,
        private MetricCounterRepositoryInterface $metricCounterRepository,
    ) {
    }

    public function getEstimate(Subscription $subscription)
    {
    }
}
