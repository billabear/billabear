<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Stats;

use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountDailyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountMonthlyStatsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\SubscriptionCountYearlyStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class CreateSubscriptionCountStats
{
    use LoggerAwareTrait;

    public function __construct(
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionCountDailyStatsRepositoryInterface $subscriptionCountDailyStatsRepository,
        private SubscriptionCountMonthlyStatsRepositoryInterface $subscriptionCountMonthlyStatsRepository,
        private SubscriptionCountYearlyStatsRepositoryInterface $subscriptionCountYearlyStatsRepository,
    ) {
    }

    public function generate()
    {
        $this->getLogger()->info('Start create subscription count stats');
        try {
            $oldestSubscription = $this->subscriptionRepository->getOldestSubscription();
            $now = new \DateTime('now');

            $startDate = clone $oldestSubscription->getCreatedAt();
            if ($startDate instanceof \DateTimeImmutable) {
                $startDate = \DateTime::createFromImmutable($startDate);
            }
            $brands = $this->brandSettingsRepository->getAll();

            foreach ($brands as $brand) {
                while ($startDate < $now) {
                    $endDate = clone $startDate;
                    $endDate->modify('+1 day');
                    $this->getLogger()->info('Getting stats for day', ['startDate' => $startDate, 'endDate' => $endDate, 'brand_code' => $brand->getCode()]);

                    $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                    $dayStat = $this->subscriptionCountDailyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                    $dayStat->setCount($dayStatCount);
                    $this->subscriptionCountDailyStatsRepository->save($dayStat);
                    $startDate = $endDate;
                }
                $startDate = clone $oldestSubscription->getCreatedAt();
                if ($startDate instanceof \DateTimeImmutable) {
                    $startDate = \DateTime::createFromImmutable($startDate);
                }

                while ($startDate < $now) {
                    $startDate->modify('first day of this month');
                    $endDate = clone $startDate;
                    $endDate->modify('+1 month');
                    $this->getLogger()->info('Getting stats for month', ['startDate' => $startDate, 'endDate' => $endDate, 'brand_code' => $brand->getCode()]);

                    $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                    $monthStat = $this->subscriptionCountMonthlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                    $monthStat->setCount($dayStatCount);
                    $this->subscriptionCountMonthlyStatsRepository->save($monthStat);
                    $startDate = $endDate;
                }

                $startDate = clone $oldestSubscription->getCreatedAt();
                if ($startDate instanceof \DateTimeImmutable) {
                    $startDate = \DateTime::createFromImmutable($startDate);
                }
                while ($startDate < $now) {
                    $startDate->modify('first day of this year');
                    $endDate = clone $startDate;
                    $endDate->modify('+1 year');
                    $this->getLogger()->info('Getting stats for year', ['startDate' => $startDate, 'endDate' => $endDate, 'brand_code' => $brand->getCode()]);

                    $dayStatCount = $this->subscriptionRepository->getActiveCountForPeriod($startDate, $endDate, $brand);
                    $yearStat = $this->subscriptionCountYearlyStatsRepository->getStatForDateTime($startDate, $brand->getCode());
                    $yearStat->setCount($dayStatCount);
                    $this->subscriptionCountYearlyStatsRepository->save($dayStat);
                    $startDate = $endDate;
                }
            }
        } catch (NoEntityFoundException $e) {
            $this->getLogger()->error('Unable to process stats', ['exception_message' => $e->getMessage()]);
        }
    }
}
