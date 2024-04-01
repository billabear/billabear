<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
            $lastStat = $lastStatQb->getQuery()->getResult()[0] ?? null;

            if ($lastStat instanceof SubscriptionCountMonthlyStats) {
                $stat->setCount($lastStat->getCount());
            }
        }

        return $stat;
    }
}
