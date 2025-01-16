<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
