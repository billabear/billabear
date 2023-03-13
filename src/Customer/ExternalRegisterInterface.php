<?php

namespace App\Customer;

use App\Entity\Customer;

interface ExternalRegisterInterface
{
    public function register(Customer $customer): Customer;
}
