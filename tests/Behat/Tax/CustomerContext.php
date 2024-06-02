<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

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
