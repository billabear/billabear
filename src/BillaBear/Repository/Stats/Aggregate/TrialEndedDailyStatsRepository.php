<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\TrialEndedDailyStats;

class TrialEndedDailyStatsRepository extends AbstractAmountRepository implements TrialEndedDailyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): TrialEndedDailyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof TrialEndedDailyStats) {
            $stat = new TrialEndedDailyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
