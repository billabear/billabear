<?php

namespace App\Customer;

use Parthenon\Billing\Entity\CustomerInterface;

class DummyRegister implements ExternalRegisterInterface
{
    public function register(CustomerInterface $customer): CustomerInterface
    {
        $customer->setExternalCustomerReference(bin2hex(random_bytes(32)));

        return $customer;
    }
}
