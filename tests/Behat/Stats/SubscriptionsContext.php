<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Stats;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Tests\Behat\SendRequestTrait;

class SubscriptionsContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session)
    {
    }

    /**
     * @When I view the subscription stats
     */
    public function iViewTheSubscriptionStats()
    {
        $this->sendJsonRequest('GET', '/app/reports/subscriptions');
    }

    /**
     * @Then I will see the subscription count for :arg1 as :arg2
     */
    public function iWillSeeTheSubscriptionCountForAs($arg1, $count)
    {
        $data = $this->getJsonContent();

        if (!isset($data['subscriptions'])) {
            throw new \Exception('No subscriptions');
        }

        foreach ($data['subscriptions'] as $subscription) {
            if ($subscription['name'] === $arg1) {
                if ($subscription['count'] == $count) {
                    return;
                } else {
                    throw new \Exception('subscription count found - '.$count);
                }
            }
        }
        throw new \Exception('no subscription found');
    }

    /**
     * @Then I will see the yearly subscription count as :arg1
     */
    public function iWillSeeTheYearlySubscriptionCountAs($count)
    {
        $data = $this->getJsonContent();

        if (!isset($data['schedule'])) {
            throw new \Exception('No schedules');
        }

        foreach ($data['schedule'] as $subscription) {
            if ('year' === $subscription['name']) {
                if ($subscription['count'] == $count) {
                    return;
                } else {
                    throw new \Exception('schedules count found - '.$count);
                }
            }
        }
        throw new \Exception('no schedules found');
    }

    /**
     * @Then I will see the monthy subscription count as :arg1
     */
    public function iWillSeeTheMonthySubscriptionCountAs($count)
    {
        $data = $this->getJsonContent();

        if (!isset($data['schedule'])) {
            throw new \Exception('No schedules');
        }

        foreach ($data['schedule'] as $subscription) {
            if ('month' === $subscription['name']) {
                if ($subscription['count'] == $count) {
                    return;
                } else {
                    throw new \Exception('schedules count found - '.$count);
                }
            }
        }
        throw new \Exception('no schedules found');
    }
}
