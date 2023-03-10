<?php

namespace App\Factory;

use App\Dto\CreateCustomerDto;
use App\Entity\Customer;
use Parthenon\Common\Address;

class CustomerFactory
{
    public function createCustomer(CreateCustomerDto $createCustomerDto) : Customer
    {
        $address = new Address();
        $address->setCountry($createCustomerDto->getCountry());

        $customer = new Customer();
        $customer->setBillingEmail($createCustomerDto->getEmail());
        $customer->setReference($createCustomerDto->getReference());
        $customer->setExternalCustomerReference('');
        $customer->setBillingAddress($address);

        return $customer;
    }
}