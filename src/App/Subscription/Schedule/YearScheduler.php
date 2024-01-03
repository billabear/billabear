<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
