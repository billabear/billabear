<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Subscription\Schedule;

use Parthenon\Billing\Entity\Subscription;

class MonthScheduler implements SchedulerInterface
{
    public function scheduleNextDueDate(Subscription $subscription): void
    {
        $createdAt = clone $subscription->getCreatedAt();
        $nextBill = clone $subscription->getValidUntil();
        $monthDate = clone $subscription->getValidUntil();
        $monthDate->modify('first day of this month');
        $monthDate->modify('+1 month');

        $expectedMonth = $monthDate->format('m');
        $expectedDay = $createdAt->format('d');

        $nextBill->modify('+1 month');

        if ($nextBill->format('m') === $expectedMonth && $nextBill->format('d') === $expectedDay) {
            $subscription->setValidUntil($nextBill);

            return;
        }
        if ($nextBill->format('m') > $expectedMonth) {
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
