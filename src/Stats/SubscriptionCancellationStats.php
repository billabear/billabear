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

use App\Entity\Customer;
use App\Repository\Stats\SubscriptionCancellationDailyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCancellationStats
{
    public function __construct(
        private SubscriptionCancellationDailyStatsRepositoryInterface $dailyStatusRepository,
        private SubscriptionCancellationMonthlyStatsRepositoryInterface $weeklyStatusRepository,
        private SubscriptionCancellationYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function handleStats(Subscription $subscription)
    {
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        $brandCode = $customer->getBrand();

        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->weeklyStatusRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->weeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->yearlyStatsRepository->getStatForDateTime($subscription->getEndedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->yearlyStatsRepository->save($yearStat);
    }
}
