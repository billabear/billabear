<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Subscription\Schedule;

use BillaBear\Subscription\Schedule\EndOfMonthScheduler;

class EndOfTheMonthSchedulerTest extends \PHPUnit\Framework\TestCase
{
    public function testEndOfMonthWithStartOfMonth()
    {
        $subject = new EndOfMonthScheduler();
        $datetime = $subject->testableScheduleNextDueDate(new \DateTime('2023-12-01'));

        $this->assertEquals('2023-12-31', $datetime->format('Y-m-d'));
    }

    public function testEndOfMonthWithEndOfMonthLeapYear()
    {
        $subject = new EndOfMonthScheduler();
        $datetime = $subject->testableScheduleNextDueDate(new \DateTime('2024-01-31'));

        $this->assertEquals('2024-02-29', $datetime->format('Y-m-d'));
    }

    public function testEndOfMonthWithEndOfMonth()
    {
        $subject = new EndOfMonthScheduler();
        $datetime = $subject->testableScheduleNextDueDate(new \DateTime('2023-01-31'));

        $this->assertEquals('2023-02-28', $datetime->format('Y-m-d'));
    }

    public function testEndOfMonthWithMiddleOfMonth()
    {
        $subject = new EndOfMonthScheduler();
        $datetime = $subject->testableScheduleNextDueDate(new \DateTime('2023-12-12'));

        $this->assertEquals('2023-12-31', $datetime->format('Y-m-d'));
    }
}
