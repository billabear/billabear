<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Schedule;

use Parthenon\Billing\Entity\Subscription;

class EndOfMonthScheduler implements SchedulerInterface
{
    public function scheduleNextDueDate(Subscription $subscription): void
    {
        $date = $this->testableScheduleNextDueDate($subscription->getValidUntil() ?? $subscription->getCreatedAt());
        $subscription->setValidUntil($date);
    }

    public function testableScheduleNextDueDate(\DateTime $dateTime): \DateTime
    {
        $date = clone $dateTime;
        $date->modify('last day of this month');
        if ($date->format('Y-m-d') === $dateTime->format('Y-m-d')) {
            $date->modify('last day of next month');
        }

        return $date;
    }
}
