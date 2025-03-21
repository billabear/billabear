<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\SubscriptionCountDailyStats;

class SubscriptionCountDailyStatsRepository extends AbstractAmountRepository implements SubscriptionCountDailyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): SubscriptionCountDailyStats
    {
        $year = $dateTime->format('Y');
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof SubscriptionCountDailyStats) {
            $stat = new SubscriptionCountDailyStats();
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

            if ($lastStat instanceof SubscriptionCountDailyStats) {
                $stat->setCount($lastStat->getCount());
            }
        }

        return $stat;
    }
}
