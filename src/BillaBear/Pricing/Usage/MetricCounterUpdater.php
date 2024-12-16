<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

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
        $this->getLogger()->info('Updating metric counter for subscription', ['subscription_id' => (string) $subscription->getId()]);

        /** @var Price $price */
        $price = $subscription->getPrice();
        if (!$price->getUsage()) {
            $this->getLogger()->warning('Tried to update metric counter for subscription that does not have usage price', ['subscription_id' => (string) $subscription->getId()]);

            return;
        }

        $customer = $subscription->getCustomer();
        $metric = $price->getMetric();

        $counter = $this->metricCounterRepository->getForCustomerAndMetric($customer, $metric);
        // Not updating
        if ($counter->hasBeenUpdated()) {
            $when = $counter->getUpdatedAt();
        } else {
            // Questions should be raised about why made that nullable in the first place...
            $when = $subscription->getStartOfCurrentPeriod() ?? new \DateTime('-60 seconds');
        }

        $metricValue = $this->metricProvider->getMetricForDateTime($subscription, $when);

        $this->getLogger()->info('Updating metric counter for subscription', [
            'subscription_id' => (string) $subscription->getId(),
            'metric_id' => (string) $metric->getId(),
            'metric_value' => (float) $metricValue,
            'when' => (string) $when->format('Y-m-d H:i:s'),
        ]
        );
        $counter->addValue($metricValue);
        $this->metricCounterRepository->save($counter);
    }
}
