<?php

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
