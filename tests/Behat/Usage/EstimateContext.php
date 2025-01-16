<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Usage;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class EstimateContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @When I request the current cost estimate for :arg1
     */
    public function iRequestTheCurrentCostEstimateFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/costs');
    }

    /**
     * @Then I will be told the cost estimate is :arg2 :arg1
     */
    public function iWillBeToldTheCostEstimateIs($amount, $currency)
    {
        $data = $this->getJsonContent();

        if ($data['total']['amount'] !== intval($amount)) {
            var_dump($data);
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $data['total']['amount']));
        }
        if ($data['total']['currency'] !== $currency) {
            throw new \Exception(sprintf('Expected %s but got %s as a currency', $currency, $data['total']['currency']));
        }
    }
}
