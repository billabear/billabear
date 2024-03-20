<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription\Schedule;

use Parthenon\Billing\Entity\Subscription;

class MonthScheduler implements SchedulerInterface
{
    public function scheduleNextDueDate(Subscription $subscription): void
    {
        $createdAt = clone $subscription->getCreatedAt();
        $nextBill = clone ($subscription->getValidUntil() ?? $subscription->getCreatedAt());
        $monthDate = clone $nextBill;
        $monthDate->modify('first day of this month');
        $monthDate->modify('+1 month');
        $secondMonth = clone $monthDate;
        $secondMonth->modify('+1 month');

        $expectedMonth = $monthDate->format('m');
        $expectedDay = $createdAt->format('d');

        $nextBill->modify('+1 month');

        if ($nextBill->format('m') === $expectedMonth && $nextBill->format('d') === $expectedDay) {
            $subscription->setValidUntil($nextBill);

            return;
        }
        if ($nextBill->format('m') === $secondMonth->format('m')) {
            while ($nextBill->format('m') != $expectedMonth) {
                $nextBill->modify('-1 day');
            }
        } else {
            while ($nextBill->format('d') != $expectedDay) {
                $nextBill->modify('+1 day');
            }
        }
        $subscription->setValidUntil($nextBill);
    }
}
