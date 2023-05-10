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
use App\Repository\Stats\SubscriptionCreationDailyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCreationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCreationYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class SubscriptionCreationStats
{
    public function __construct(
        private SubscriptionCreationDailyStatsRepositoryInterface $dailyStatusRepository,
        private SubscriptionCreationMonthlyStatsRepositoryInterface $weeklyStatusRepository,
        private SubscriptionCreationYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function handleStats(Subscription $subscription)
    {
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        $brandCode = $customer->getBrand();

        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->weeklyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->weeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->yearlyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->yearlyStatsRepository->save($yearStat);
    }
}
