<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Stats;

use App\Repository\Orm\BrandSettingsRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class FinancialContext implements Context
{
    use SendRequestTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
        private BrandSettingsRepository $brandSettingsRepository,
    ) {
    }

    /**
     * @When I view the lifetime value:
     */
    public function iViewTheLifetimeValue(?TableNode $table = null)
    {
        $filters = [];

        $row = $table?->getRowsHash();
        if (isset($row['Country'])) {
            $filters['country'] = $row['Country'];
        }

        if (isset($row['Payment Schedule'])) {
            $filters['payment_schedule'] = $row['Payment Schedule'];
        }

        if (isset($row['Subscription Plan'])) {
            $plan = $this->findSubscriptionPlanByName($row['Subscription Plan']);
            $filters['subscription_plan'] = (string) $plan->getId();
        }

        if (isset($row['Brand'])) {
            $brand = $this->brandSettingsRepository->findOneBy(['code' => $row['Brand']]);
            $filters['brand'] = (string) $brand->getId();
        }

        $filtersString = '';
        foreach ($filters as $key => $value) {
            $filtersString .= urlencode($key).'='.urlencode($value).'&';
        }
        $this->sendJsonRequest('GET', '/app/stats/lifetime?'.$filtersString);
    }

    /**
     * @Then I should see a customer average lifespan
     */
    public function iShouldSeeACustomerAverageLifespan()
    {
        $data = $this->getJsonContent();

        if (!isset($data['lifespan'])) {
            throw new \Exception('Lifespan not set');
        }
    }

    /**
     * @Then I should see a customer average lifetime value
     */
    public function iShouldSeeACustomerAverageLifetimeValue()
    {
        $data = $this->getJsonContent();

        if (!isset($data['lifetime_value'])) {
            throw new \Exception('Lifetime value not set');
        }
    }
}
