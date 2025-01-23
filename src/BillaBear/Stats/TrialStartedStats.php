<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\TrialStartedDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialStartedMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialStartedYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class TrialStartedStats
{
    public function __construct(
        private TrialStartedDailyStatsRepositoryInterface $countDailyStatsRepository,
        private TrialStartedMonthlyStatsRepositoryInterface $countWeeklyStatusRepository,
        private TrialStartedYearlyStatsRepositoryInterface $countYearlyStatsRepository,
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
