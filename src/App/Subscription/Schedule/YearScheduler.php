<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription\Schedule;

use Parthenon\Billing\Entity\Subscription;

class YearScheduler implements SchedulerInterface
{
    public function scheduleNextDueDate(Subscription $subscription): void
    {
        $expectedMonth = $subscription->getCreatedAt()->format('m');
        $date = clone ($subscription->getValidUntil() ?? $subscription->getCreatedAt());

        $date->modify('+1 year');

        if ($expectedMonth !== $date->format('m')) {
            $date->modify('-1 day');
        }

        $subscription->setValidUntil($date);
    }
}
