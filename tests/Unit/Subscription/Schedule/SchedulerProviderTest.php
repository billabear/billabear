<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
