<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Tax;

use App\Repository\Orm\CustomerRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class CustomerContext implements Context
{
    use CustomerTrait;
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @Then the customer :arg1 should have the digital tax rate :arg2
     */
    public function theCustomerShouldHaveTheDigitalTaxRate($customerEmail, $rate)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        if ($customer->getDigitalTaxRate() != $rate) {
            throw new \Exception('Got a different rate '.$customer->getDigitalTaxRate());
        }
    }

    /**
     * @Then the customer :arg1 should have the standard tax rate :arg2
     */
    public function theCustomerShouldHaveThePhysicalTaxRate($customerEmail, $rate)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        if ($customer->getStandardTaxRate() != $rate) {
            throw new \Exception('Got a different rate '.$customer->getStandardTaxRate());
        }
    }
}
