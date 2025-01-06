<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Entity\Stats\SubscriptionCancellationDailyStats;

class SubscriptionCancellationDailyStatsRepository extends AbstractAmountRepository implements SubscriptionCancellationDailyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): SubscriptionCancellationDailyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof SubscriptionCancellationDailyStats) {
            $stat = new SubscriptionCancellationDailyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
