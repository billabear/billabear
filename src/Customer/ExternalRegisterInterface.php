<?php

namespace App\Customer;

use Parthenon\Billing\Entity\CustomerInterface;

interface ExternalRegisterInterface
{
    public function register(CustomerInterface $customer): CustomerInterface;
}
