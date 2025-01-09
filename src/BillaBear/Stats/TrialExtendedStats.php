<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\TrialExtendedDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialExtendedMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialExtendedYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class TrialExtendedStats
{
    public function __construct(
        private TrialExtendedDailyStatsRepositoryInterface $countDailyStatsRepository,
        private TrialExtendedMonthlyStatsRepositoryInterface $countWeeklyStatusRepository,
        private TrialExtendedYearlyStatsRepositoryInterface $countYearlyStatsRepository,
    ) {
    }

    public function handleStats(Subscription $subscription)
    {
        /** @var Customer $customer */
        $customer = $subscription->getCustomer();
        $brandCode = $customer->getBrand();

        $dailyStat = $this->countDailyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $dailyStat->increaseCount();
        $this->countDailyStatsRepository->save($dailyStat);

        $weeklyStat = $this->countWeeklyStatusRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $weeklyStat->increaseCount();
        $this->countWeeklyStatusRepository->save($weeklyStat);

        $yearStat = $this->countYearlyStatsRepository->getStatForDateTime($subscription->getCreatedAt(), $brandCode);
        $yearStat->increaseCount();
        $this->countYearlyStatsRepository->save($yearStat);
    }
}
