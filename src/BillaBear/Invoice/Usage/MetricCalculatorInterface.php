<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Usage;

use BillaBear\Entity\Subscription;
use BillaBear\Entity\Usage\Metric;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('billabear.metric_calculator')]
interface MetricCalculatorInterface
{
    public function supports(Metric $metric): bool;

    public function updatedAfter(Subscription $subscription, \DateTime $dateTime): bool;

    public function getMonthlyValue(Subscription $subscription): float;

    public function getYearlyValue(Subscription $subscription): float;

    public function getWeeklyValue(Subscription $subscription): float;
}
