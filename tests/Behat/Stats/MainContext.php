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
use App\Entity\SubscriptionCreationWeeklyStats;
use App\Repository\Orm\SubscriptionCreationDailyStatsRepository;
use App\Repository\Orm\SubscriptionCreationWeeklyStatsRepository;
use Behat\Behat\Context\Context;

class MainContext implements Context
{
    public function __construct(
        private SubscriptionCreationDailyStatsRepository $subscriptionDailyStatsRepository,
        private SubscriptionCreationWeeklyStatsRepository $subscriptionCreationWeeklyStatsRepository,
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
     * @Then the subscriber weekly stat for the day should be :arg1
     */
    public function theSubscriberWeeklyStatForTheDayShouldBe($count)
    {
        $dateTime = new \DateTime('now');
        $statEntity = $this->subscriptionCreationWeeklyStatsRepository->findOneBy([
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
            'day' => 1,
        ]);

        if (!$statEntity instanceof SubscriptionCreationWeeklyStats) {
            throw new \Exception('No stat found');
        }

        if ($statEntity->getCount() != $count) {
            throw new \Exception('Count is wrong');
        }
    }
}
