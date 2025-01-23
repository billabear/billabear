<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Schedule;

use Parthenon\Billing\Entity\Subscription;

class OneOffScheduler implements SchedulerInterface
{
    public function scheduleNextDueDate(Subscription $subscription): void
    {
        $date = clone ($subscription->getValidUntil() ?? $subscription->getCreatedAt());

        $date->modify('+100 years');

        $subscription->setValidUntil($date);
    }
}
