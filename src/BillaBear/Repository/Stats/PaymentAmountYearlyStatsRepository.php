<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats;

use BillaBear\Entity\Stats\PaymentAmountYearlyStats;
use Brick\Money\Currency;

class PaymentAmountYearlyStatsRepository extends AbstractAmountRepository implements PaymentAmountYearlyStatsRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): PaymentAmountYearlyStats
    {
        $year = $dateTime->format('Y');
        $month = 1;
        $day = 1;
        $stat = $this->entityRepository->findOneBy([
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'currency' => $currency->getCurrencyCode(),
            'brandCode' => $brandCode,
        ]);

        if (!$stat instanceof PaymentAmountYearlyStats) {
            $stat = new PaymentAmountYearlyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setCurrency($currency->getCurrencyCode());
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
