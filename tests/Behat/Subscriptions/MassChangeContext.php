<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Subscriptions;

use App\Entity\MassSubscriptionChange;
use App\Repository\Orm\MassSubscriptionChangeRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class MassChangeContext implements Context
{
    use SendRequestTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
        private MassSubscriptionChangeRepository $massSubscriptionChangeRepository,
    ) {
    }

    /**
     * @When I create a mass subscription change:
     */
    public function iCreateAMassSubscriptionChange(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [];

        if (!isset($data['Date'])) {
            throw new \Exception('Date required');
        }

        $dateTime = new \DateTime($data['Date']);
        $payload['change_date'] = $dateTime->format(\DATE_RFC3339_EXTENDED);

        if (isset($data['Target Subscription Plan'])) {
            if ('invalid' == strtolower($data['Target Subscription Plan'])) {
                $payload['target_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['Target Subscription Plan']);
                $payload['target_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        if (isset($data['New Subscription Plan'])) {
            if ('invalid' == strtolower($data['New Subscription Plan'])) {
                $payload['new_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['New Subscription Plan']);
                $payload['new_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        $this->sendJsonRequest('POST', '/app/subscription/mass-change', $payload);
    }

    /**
     * @Then there should not be a mass subscription change
     */
    public function thereShouldNotBeAMassSubscriptionChange()
    {
        /** @var MassSubscriptionChange $one */
        $one = $this->massSubscriptionChangeRepository->findOneBy([]);

        if ($one) {
            throw new \Exception('Found mass subscription change');
        }
    }

    /**
     * @Then there should be a mass subscription change that contains:
     */
    public function thereShouldBeAMassSubscriptionChangeThatContains(TableNode $table)
    {
        $data = $table->getRowsHash();
        /** @var MassSubscriptionChange $one */
        $one = $this->massSubscriptionChangeRepository->findOneBy([]);

        if (isset($data['Target Subscription Plan'])) {
            $subscriptionPlan = $this->findSubscriptionPlanByName($data['Target Subscription Plan']);
            if ($one->getTargetSubscriptionPlan()?->getId() != $subscriptionPlan->getId()) {
                throw new \Exception('Wrong target subscription plan');
            }
        }

        if (isset($data['New Subscription Plan'])) {
            $subscriptionPlan = $this->findSubscriptionPlanByName($data['New Subscription Plan']);
            if ($one->getNewSubscriptionPlan()?->getId() != $subscriptionPlan->getId()) {
                throw new \Exception('Wrong new subscription plan');
            }
        }

        if (isset($data['Date'])) {
            $changeDate = new \DateTime($data['Date']);
            if ($changeDate->format('Y-m-d') != $one->getChangeDate()->format('Y-m-d')) {
                throw new \Exception("Date doesn't match");
            }
        }
    }
}
