<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Stats\Aggregate;

use BillaBear\Entity\Stats\SubscriptionCountYearlyStats;

class SubscriptionCountYearlyStatsRepository extends AbstractAmountRepository implements SubscriptionCountYearlyStatsRepositoryInterface
{
    public function getStatForDateTime(\DateTimeInterface $dateTime, string $brandCode): SubscriptionCountYearlyStats
    {
        $year = $dateTime->format('Y');
        $month = 1;
        $day = 1;
        $stat = $this->entityRepository->findOneBy(['year' => $year, 'month' => $month, 'day' => $day, 'brandCode' => $brandCode]);

        if (!$stat instanceof SubscriptionCountYearlyStats) {
            $stat = new SubscriptionCountYearlyStats();
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

            if ($lastStat instanceof SubscriptionCountYearlyStats) {
                $stat->setCount($lastStat->getCount());
            }
        }

        return $stat;
    }
}
