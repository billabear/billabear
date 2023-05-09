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

namespace App\Stats;

use App\Repository\SubscriptionDailyStatusRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCreationStats
{
    public function __construct(
        private SubscriptionDailyStatusRepositoryInterface $dailyStatusRepository,
    ) {
    }

    public function handleStats(Subscription $subscription)
    {
        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($subscription->getCreatedAt());
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);
    }
}
