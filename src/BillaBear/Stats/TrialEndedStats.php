<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Entity\Customer;
use BillaBear\Repository\Stats\Aggregate\TrialEndedDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialEndedMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\TrialEndedYearlyStatsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;

class TrialEndedStats
{
    public function __construct(
        private TrialEndedDailyStatsRepositoryInterface $countDailyStatsRepository,
        private TrialEndedMonthlyStatsRepositoryInterface $countWeeklyStatusRepository,
        private TrialEndedYearlyStatsRepositoryInterface $countYearlyStatsRepository,
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
