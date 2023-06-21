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

use App\Entity\Stats\SubscriptionCountMonthlyStats;

class SubscriptionCountMonthlyStatsRepository extends AbstractAmountRepository implements SubscriptionCountMonthlyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): SubscriptionCountMonthlyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = 1;
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof SubscriptionCountMonthlyStats) {
            $stat = new SubscriptionCountMonthlyStats();
            $stat->setYear($year);
            $stat->setMonth($month);
            $stat->setDay($day);
            $stat->setBrandCode($brandCode);
            $stat->setCount(0);

            $lastStatQb = $this->entityRepository->createQueryBuilder('ls');
            $lastStatQb->orderBy('ls.day', 'DESC')
                ->addOrderBy('ls.month', 'DESC')
                ->addOrderBy('ls.year', 'DESC')
                ->setMaxResults(1)
                ->andWhere('ls.brandCode = :brandCode')
                ->setParameter('brandCode', $brandCode);
            $lastStat = $lastStatQb->getQuery()->getResult();

            if ($lastStat instanceof SubscriptionCountMonthlyStats) {
                $stat->setCount($lastStat->getCount());
            }
        }

        return $stat;
    }
}
