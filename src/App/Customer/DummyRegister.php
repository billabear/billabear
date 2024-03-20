<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Customer;

use App\Entity\Customer;

class DummyRegister implements ExternalRegisterInterface
{
    public function register(Customer $customer): Customer
    {
        $customer->setExternalCustomerReference(bin2hex(random_bytes(32)));

        return $customer;
    }
}
