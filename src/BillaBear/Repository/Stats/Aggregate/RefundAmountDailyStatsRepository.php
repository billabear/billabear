<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\RefundAmountDailyStats;
use Brick\Money\Currency;

class RefundAmountDailyStatsRepository extends AbstractAmountRepository implements RefundAmountDailyStatsRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): RefundAmountDailyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $stat = $this->entityRepository->findOneBy([
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'currency' => $currency->getCurrencyCode(),
            'brandCode' => $brandCode,
        ]);

        if (!$stat instanceof RefundAmountDailyStats) {
            $stat = new RefundAmountDailyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setCurrency($currency->getCurrencyCode());
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
