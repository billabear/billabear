<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Customers;

use BillaBear\Entity\Customer;
use BillaBear\Entity\ManageCustomerSession;

trait CustomerTrait
{
    /**
     * @throws \Exception
     */
    public function getSession($email): ManageCustomerSession
    {
        $customer = $this->getCustomerByEmail($email);

        $session = $this->manageCustomerSessionRepository->findOneBy(['customer' => $customer]);
        if (!$session instanceof ManageCustomerSession) {
            throw new \Exception('Did not find customer session');
        }

        return $session;
    }

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
