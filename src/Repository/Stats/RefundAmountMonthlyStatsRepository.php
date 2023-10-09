<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository\Stats;

use App\Entity\Stats\RefundAmountMonthlyStats;
use Brick\Money\Currency;

class RefundAmountMonthlyStatsRepository extends AbstractAmountRepository implements RefundAmountMonthlyStatsRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): RefundAmountMonthlyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = 1;
        $stat = $this->entityRepository->findOneBy([
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'currency' => $currency->getCurrencyCode(),
            'brandCode' => $brandCode,
            ]);

        if (!$stat instanceof RefundAmountMonthlyStats) {
            $stat = new RefundAmountMonthlyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setCurrency($currency->getCurrencyCode());
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
