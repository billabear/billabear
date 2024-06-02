<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Customers;

use BillaBear\Entity\Customer;

trait CustomerTrait
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function getCustomerByEmail($email): Customer
    {
        $customer = $this->customerRepository->findOneBy(['billingEmail' => $email]);

        if (!$customer instanceof Customer) {
            throw new \Exception(sprintf("No customer for '%s'", $email));
        }

        $this->customerRepository->getEntityManager()->refresh($customer);

        return $customer;
    }
}
