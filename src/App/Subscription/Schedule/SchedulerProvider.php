<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription\Schedule;

use Parthenon\Billing\Entity\Price;

class SchedulerProvider
{
    public function getScheduler(Price $price): SchedulerInterface
    {
        if ('week' === $price->getSchedule()) {
            return new WeekScheduler();
        }

        if ('month' === $price->getSchedule()) {
            return new MonthScheduler();
        }

        if ('year' === $price->getSchedule()) {
            return new YearScheduler();
        }

        return new OneOffScheduler();
    }
}
