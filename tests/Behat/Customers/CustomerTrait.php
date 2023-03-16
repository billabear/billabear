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

namespace App\Tests\Behat\Customers;

use App\Entity\Customer;

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
