<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Subscription\Schedule;

use BillaBear\Subscription\Schedule\YearScheduler;
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
