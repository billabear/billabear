<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Usage;

use BillaBear\Invoice\Usage\MetricCounterUpdater;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class CounterUpdate
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly MetricCounterUpdater $metricCounterUpdater,
    ) {
    }

    public function execute(): void
    {
        $this->getLogger()->info('Executing metric counter updater');

        // TODO: Change to find unqiue customer ids for events sent in last 60 seconds
        $subscriptions = $this->subscriptionRepository->getSubscriptionWithUsage();

        // TODO: Loop through customers and update all counters and estimates.
        foreach ($subscriptions as $subscription) {
            $this->metricCounterUpdater->updateForSubscription($subscription);
        }

        // TODO: Estimate cost for all subscriptions
    }
}
