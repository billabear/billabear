<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pricing\Usage;

use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\Metric;
use BillaBear\Repository\Usage\EventRepositoryInterface;

class SumMetricCalculator implements MetricCalculatorInterface
{
    public function __construct(private EventRepositoryInterface $eventRepository)
    {
    }

    public function getMonthlyValue(Subscription $subscription): float
    {
        return $this->eventRepository->getSumForMonth($subscription->getCustomer(), $subscription->getPrice()->getMetric(), $subscription);
    }

    public function getYearlyValue(Subscription $subscription): float
    {
        return $this->eventRepository->getSumForYear($subscription->getCustomer(), $subscription->getPrice()->getMetric(), $subscription);
    }

    public function getWeeklyValue(Subscription $subscription): float
    {
        return $this->eventRepository->getSumForWeek($subscription->getCustomer(), $subscription->getPrice()->getMetric(), $subscription);
    }

    public function supports(Metric $metric): bool
    {
        return MetricAggregationMethod::SUM === $metric->getAggregationMethod();
    }

    public function updatedAfter(Subscription $subscription, \DateTime $dateTime): bool
    {
        return $this->eventRepository->getEventCountAfterDateTime($subscription->getCustomer(), $subscription->getPrice()->getMetric(), $subscription, $dateTime) > 0;
    }

    public function getDateTimeValue(Subscription $subscription, \DateTime $dateTime): float
    {
        return $this->eventRepository->getSumForDateTime($subscription->getCustomer(), $subscription->getPrice()->getMetric(), $subscription, $dateTime);
    }
}
