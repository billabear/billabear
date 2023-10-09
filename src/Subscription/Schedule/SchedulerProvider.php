<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
