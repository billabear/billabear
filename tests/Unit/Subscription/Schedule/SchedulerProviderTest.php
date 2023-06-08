<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Subscription\Schedule;

use App\Subscription\Schedule\MonthScheduler;
use App\Subscription\Schedule\SchedulerProvider;
use App\Subscription\Schedule\WeekScheduler;
use App\Subscription\Schedule\YearScheduler;
use Parthenon\Billing\Entity\Price;
use PHPUnit\Framework\TestCase;

class SchedulerProviderTest extends TestCase
{
    public function testReturnWeekScheduler()
    {
        $price = new Price();
        $price->setSchedule('week');
        $subject = new SchedulerProvider();
        $this->assertInstanceOf(WeekScheduler::class, $subject->getScheduler($price));
    }

    public function testReturnMonthScheduler()
    {
        $price = new Price();
        $price->setSchedule('month');
        $subject = new SchedulerProvider();
        $this->assertInstanceOf(MonthScheduler::class, $subject->getScheduler($price));
    }

    public function testReturnYearScheduler()
    {
        $price = new Price();
        $price->setSchedule('year');
        $subject = new SchedulerProvider();
        $this->assertInstanceOf(YearScheduler::class, $subject->getScheduler($price));
    }
}
