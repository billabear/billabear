<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Interopt\Stripe;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class SubscriptionsContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
    ) {
    }

    /**
     * @When I fetch the subscription list from the stripe interopt layer
     */
    public function iFetchTheSubscriptionListFromTheStripeInteroptLayer()
    {
        $this->isStripe(true);
        $this->sendJsonRequest('GET', '/interopt/stripe/v1/subscriptions');
    }

    /**
     * @Then I will see a subscription in the stripe interopt list for :arg1
     */
    public function iWillSeeASubscriptionInTheStripeInteroptListFor($planName)
    {
        $data = $this->getJsonContent();

        if ('list' !== $data['object']) {
            throw new \Exception('Expected a list response');
        }

        foreach ($data['data'] as $subscription) {
            if ($subscription['metadata']['plan_name'] == $planName) {
                return;
            }
        }

        throw new \Exception('Could not find subscription');
    }
}
