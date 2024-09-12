<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Usage;

use BillaBear\Entity\Price;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\Usage\MetricCounterRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class MetricCounterUpdater
{
    use LoggerAwareTrait;

    public function __construct(
        private MetricCounterRepositoryInterface $metricCounterRepository,
        private MetricProvider $metricProvider,
    ) {
    }

    public function updateForSubscription(Subscription $subscription): void
    {
        /** @var Price $price */
        $price = $subscription->getPrice();
        if (!$price->getUsage()) {
            $this->getLogger()->warning('Tried to update metric counter for subscription that does not have usage price', ['subscription_id' => (string) $subscription->getId()]);

            return;
        }

        $customer = $subscription->getCustomer();
        $metric = $price->getMetric();

        $counter = $this->metricCounterRepository->getForCustomerAndMetric($customer, $metric);
        if ($counter->hasBeenUpdated()) {
            $when = $counter->getUpdatedAt();
        } else {
            // Questions should be raised about why made that nullable in the first place...
            $when = $subscription->getStartOfCurrentPeriod() ?? new \DateTime('now');
        }

        $metric = $this->metricProvider->getMetricForDateTime($subscription, $when);
        $counter->addValue($metric);
        $this->metricCounterRepository->save($counter);
    }
}
