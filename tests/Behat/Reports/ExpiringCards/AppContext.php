<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Reports\ExpiringCards;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class AppContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session)
    {
    }

    /**
     * @When I view the expiring cards page
     */
    public function iViewTheExpiringCardsPage()
    {
        $this->sendJsonRequest('GET', '/app/reports/expiring-cards');
    }

    /**
     * @Then I will see there is an expiring card for :arg1 with the last for :arg2
     */
    public function iWillSeeThereIsAnExpiringCardForWithTheLastFor($customerEmail, $lastFour)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $card) {
            if ($card['customer']['email'] == $customerEmail && $card['payment_card']['last_four'] == $lastFour) {
                return;
            }
        }

        throw new \Exception("Couldn't see expired card");
    }

    /**
     * @Then I will not see there is an expiring card for :arg1 with the last for :arg2
     */
    public function iWillNotSeeThereIsAnExpiringCardForWithTheLastFor($customerEmail, $lastFour)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $card) {
            if ($card['customer']['email'] == $customerEmail && $card['payment_card']['last_four'] == $lastFour) {
                throw new \Exception('Could see expired card');
            }
        }
    }
}
