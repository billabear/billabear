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

namespace App\Tests\Behat\Stats;

use App\Entity\SubscriptionCreationDailyStats;
use App\Entity\SubscriptionCreationMonthlyStats;
use App\Entity\SubscriptionCreationYearlyStats;
use App\Repository\Orm\SubscriptionCreationDailyStatsRepository;
use App\Repository\Orm\SubscriptionCreationMonthlyStatsRepository;
use App\Repository\Orm\SubscriptionCreationYearlyStatsRepository;
use Behat\Behat\Context\Context;

class MainContext implements Context
{
    public function __construct(
        private SubscriptionCreationDailyStatsRepository $subscriptionDailyStatsRepository,
        private SubscriptionCreationMonthlyStatsRepository $subscriptionCreationMonthlyStatsRepository,
        private SubscriptionCreationYearlyStatsRepository $subscriptionCreationYearlyStatsRepository,
    ) {
    }

    /**
     * @Then the subscriber daily stat for the day should be :arg1
     */
    public function theSubscriberDailyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionDailyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => $dateTime->format('d'),
            ]);

        if (!$statEntity instanceof SubscriptionCreationDailyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the subscriber monthly stat for the day should be :arg1
     */
    public function theSubscriberWeeklyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionCreationMonthlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => 1,
        ]);

        if (!$statEntity instanceof SubscriptionCreationMonthlyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }

    /**
     * @Then the subscriber yearly stat for the day should be :arg1
     */
    public function theSubscriberYearlyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionCreationYearlyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => 1,
            'day' => 1,
        ]);

        if (!$statEntity instanceof SubscriptionCreationYearlyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }
}
