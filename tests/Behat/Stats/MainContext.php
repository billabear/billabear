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
use App\Repository\Orm\SubscriptionCreationDailyStatsRepository;
use Behat\Behat\Context\Context;

class MainContext implements Context
{
    public function __construct(
        private SubscriptionCreationDailyStatsRepository $subscriptionDailyStatsRepository,
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
}
