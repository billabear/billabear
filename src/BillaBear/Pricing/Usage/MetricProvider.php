<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

use BillaBear\Entity\Price;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\Metric;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class MetricProvider
{
    public function __construct(
        #[AutowireIterator('billabear.metric_calculator')]
        private iterable $calculators,
    ) {
    }

    public function getMetric(Subscription $subscription): float
    {
        /** @var Price $price */
        $price = $subscription->getPrice();
        $calculator = $this->getCalculator($price->getMetric());

        return match ($price->getSchedule()) {
            'year' => $calculator->getYearlyValue($subscription),
            'week' => $calculator->getWeeklyValue($subscription),
            default => $calculator->getMonthlyValue($subscription),
        };
    }

    public function getMetricForDateTime(Subscription $subscription, \DateTime $dateTime): float
    {
        /** @var Price $price */
        $price = $subscription->getPrice();
        $calculator = $this->getCalculator($price->getMetric());

        return $calculator->getDateTimeValue($subscription, $dateTime);
    }

    private function getCalculator(Metric $metric): MetricCalculatorInterface
    {
        /** @var MetricCalculatorInterface $calculator */
        foreach ($this->calculators as $calculator) {
            if ($calculator->supports($metric)) {
                return $calculator;
            }
        }

        throw new \Exception('Unable to find calculator for metric type '.$metric->getAggregationMethod()->value);
    }
}
