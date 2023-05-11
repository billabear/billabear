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

use App\Entity\CancellationRequest;
use App\Entity\Customer;
use App\Repository\Stats\SubscriptionCancellationDailyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationMonthlyStatsRepositoryInterface;
use App\Repository\Stats\SubscriptionCancellationYearlyStatsRepositoryInterface;

class SubscriptionCancellationStats
{
    public function __construct(
        private SubscriptionCancellationDailyStatsRepositoryInterface $dailyStatusRepository,
        private SubscriptionCancellationMonthlyStatsRepositoryInterface $weeklyStatusRepository,
        private SubscriptionCancellationYearlyStatsRepositoryInterface $yearlyStatsRepository,
    ) {
    }

    public function handleStats(CancellationRequest $cancellationRequest)
    {
        /** @var Customer $customer */
        $customer = $cancellationRequest->getSubscription()->getCustomer();
        $brandCode = $customer->getBrand();

        $dailyStat = $this->dailyStatusRepository->getStatForDateTime($cancellationRequest->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->dailyStatusRepository->save($dailyStat);

        $weeklyStat = $this->weeklyStatusRepository->getStatForDateTime($cancellationRequest->getCreatedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->weeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->yearlyStatsRepository->getStatForDateTime($cancellationRequest->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->yearlyStatsRepository->save($yearStat);
    }
}
