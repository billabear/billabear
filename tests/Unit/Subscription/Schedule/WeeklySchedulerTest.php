<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Subscription\Schedule;

use BillaBear\Subscription\Schedule\WeekScheduler;
use Parthenon\Billing\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class WeeklySchedulerTest extends TestCase
{
    public function testScheduleValidUntilNextWeek()
    {
        $subscription = new Subscription();
        $subscription->setValidUntil(new \DateTime('2023-05-17'));
        $subject = new WeekScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-05-24', $subscription->getValidUntil()->format('Y-m-d'));
    }
}
