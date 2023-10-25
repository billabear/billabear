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

namespace App\Tests\Behat\Stats;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class FinancialContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
    ) {
    }

    /**
     * @When I view the lifetime value:
     */
    public function iViewTheLifetimeValue(TableNode $table = null)
    {
        $this->sendJsonRequest('GET', '/app/stats/lifetime');
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
