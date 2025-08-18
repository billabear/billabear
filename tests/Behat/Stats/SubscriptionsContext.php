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
     * @When I view the new subscriptions stats
     */
    public function iViewTheNewSubscriptionsStats()
    {
        $this->sendJsonRequest('GET', '/app/reports/subscriptions/new');
    }

    /**
     * @Then I should see :arg1 months of new subscriptions stats
     */
    public function iShouldSeeMonthsOfNewSubscriptionsStats($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        if (count($data['months']) != $arg1) {
            throw new \Exception(sprintf('Expected %d months, got %d', $arg1, count($data['months'])));
        }
    }

    /**
     * @Then I should see the total number of existing subscriptions for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfExistingSubscriptionsForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['existing'])) {
                throw new \Exception('No existing subscriptions data found for month: '.$month['month']);
            }
        }
    }

    /**
     * @Then I should see the total number of new subscriptions for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfNewSubscriptionsForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['new'])) {
                throw new \Exception('No new subscriptions data found for month: '.$month['month']);
            }
        }
    }

    /**
     * @Then I should see the total number of upgrades for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfUpgradesForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['upgrades'])) {
                throw new \Exception('No upgrades data found for month: '.$month['month']);
            }
        }
    }

    /**
     * @Then I should see the total number of downgrades for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfDowngradesForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['downgrades'])) {
                throw new \Exception('No downgrades data found for month: '.$month['month']);
            }
        }
    }

    /**
     * @Then I should see the total number of cancellations for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfCancellationsForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['cancellations'])) {
                throw new \Exception('No cancellations data found for month: '.$month['month']);
            }
        }
    }

    /**
     * @Then I should see the total number of reactivations for the last :arg1 months
     */
    public function iShouldSeeTheTotalNumberOfReactivationsForTheLastMonths($arg1)
    {
        $data = $this->getJsonContent();

        if (!isset($data['months']) || !is_array($data['months'])) {
            throw new \Exception('No months data found in response');
        }

        foreach ($data['months'] as $month) {
            if (!isset($month['reactivations'])) {
                throw new \Exception('No reactivations data found for month: '.$month['month']);
            }
        }
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
