<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Subscription\Schedule;

use App\Subscription\Schedule\MonthScheduler;
use Parthenon\Billing\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class MonthSchedulerTest extends TestCase
{
    public function testFirstOfMonth()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2023-01-01'));
        $subscription->setValidUntil(new \DateTime('2023-02-01'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-03-01', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLastOfMonthFeburary()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2022-05-31'));
        $subscription->setValidUntil(new \DateTime('2023-01-31'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-02-28', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLastOfMonthFeburaryLeapYearDay()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-02-29'));
        $subscription->setValidUntil(new \DateTime('2023-01-29'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-02-28', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLastOfMonthJanuary()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-01-31'));
        $subscription->setValidUntil(new \DateTime('2023-02-28'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-03-31', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLastOfMonthJanuaryIntoApril()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-01-31'));
        $subscription->setValidUntil(new \DateTime('2023-03-31'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-04-30', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testLastOfMonthJanuaryApril()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-01-31'));
        $subscription->setValidUntil(new \DateTime('2023-03-30'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-05-31', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testMidMonth()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-01-12'));
        $subscription->setValidUntil(new \DateTime('2023-02-12'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);

        $this->assertEquals('2023-03-12', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testEndOfMonthFullYear()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2020-01-31'));
        $subscription->setValidUntil(new \DateTime('2022-12-30'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-01-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-02-28', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-03-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-04-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-05-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-06-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-07-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-08-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-09-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-10-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-11-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-12-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2024-01-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2024-02-29', $subscription->getValidUntil()->format('Y-m-d'));
    }

    public function testEndOfMonthFullYearNoValidUntil()
    {
        $subscription = new Subscription();
        $subscription->setCreatedAt(new \DateTime('2022-12-31'));

        $subject = new MonthScheduler();
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-01-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-02-28', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-03-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-04-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-05-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-06-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-07-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-08-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-09-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-10-31', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-11-30', $subscription->getValidUntil()->format('Y-m-d'));
        $subject->scheduleNextDueDate($subscription);
        $this->assertEquals('2023-12-31', $subscription->getValidUntil()->format('Y-m-d'));
    }
}
