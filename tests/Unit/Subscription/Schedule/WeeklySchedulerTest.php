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

namespace App\Tests\Unit\Subscription\Schedule;

use App\Subscription\Schedule\WeekScheduler;
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
