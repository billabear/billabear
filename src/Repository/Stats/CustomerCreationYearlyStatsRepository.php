<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Repository\Stats;

use App\Entity\Stats\CustomerCreationYearlyStats;

class CustomerCreationYearlyStatsRepository extends AbstractAmountRepository implements CustomerCreationYearlyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): CustomerCreationYearlyStats
    {
        $year = $dateTime->format('Y');
        $month = 1;
        $day = 1;
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof CustomerCreationYearlyStats) {
            $stat = new CustomerCreationYearlyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setBrandCode($brandCode);
        }

        return $stat;
    }
}
