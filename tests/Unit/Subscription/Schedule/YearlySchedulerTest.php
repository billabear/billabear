<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Subscription\Schedule;

use App\Subscription\Schedule\YearScheduler;
use Parthenon\Billing\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class YearlySchedulerTest extends TestCase
{
    public function testFirstOfMonth()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2023-02-01'));
        $subscription->setValidUntil(new \DateTime('2023-02-01'));

        $subject = new YearScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2024-02-01', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLeapYear()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-02-29'));
        $subscription->setCreatedAt(new \DateTime('2020-02-29'));

        $subject = new YearScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2021-02-28', $subscription->getValidUntil()->format('Y-m-d'));
    }
}
