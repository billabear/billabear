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

namespace App\Tests\Behat\Payments;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class ApiContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session)
    {
    }

    /**
     * @When I view the payment list via the API
     */
    public function iViewThePaymentListViaTheApi()
    {
        $this->sendJsonRequest('GET', '/api/v1/payment');
    }

    /**
     * @Then I will see a payment for :arg1 for :arg2 in the list
     */
    public function iWillSeeAPaymentForForInTheList($customerEmail, $amount)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $payment) {
            if ($payment['amount'] == $amount && $payment['customer']['email'] == $customerEmail) {
                return;
            }
        }

        throw new \Exception("Can't find payment");
    }
}
