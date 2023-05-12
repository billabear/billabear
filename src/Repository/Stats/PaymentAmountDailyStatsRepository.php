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

namespace App\Repository\Stats;

use App\Entity\Stats\PaymentAmountDailyStats;
use Brick\Money\Currency;

class PaymentAmountDailyStatsRepository extends AbstractAmountRepository implements PaymentAmountDailyStatsRepositoryInterface
{
    public function getStatForDateTimeAndCurrency(\DateTimeInterface $dateTime, Currency $currency, string $brandCode): PaymentAmountDailyStats
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

        if (!$stat instanceof PaymentAmountDailyStats) {
            $stat = new PaymentAmountDailyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setCurrency($currency->getCurrencyCode());
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
