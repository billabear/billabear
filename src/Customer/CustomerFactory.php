<?php

namespace App\Customer;

use App\Dto\CreateCustomerDto;
use App\Entity\Customer;
use Parthenon\Common\Address;

class CustomerFactory
{
    public function createCustomer(CreateCustomerDto $createCustomerDto): Customer
    {
        $address = new Address();
        $address->setStreetLineOne($createCustomerDto->getAddress()->getStreetLineOne());
        $address->setStreetLineTwo($createCustomerDto->getAddress()->getStreetLineTwo());
        $address->setCountry($createCustomerDto->getAddress()->getCountry());
        $address->setCity($createCustomerDto->getAddress()->getCity());
        $address->setRegion($createCustomerDto->getAddress()->getRegion());
        $address->setPostcode($createCustomerDto->getAddress()->getPostcode());

        $customer = new Customer();
        $customer->setBillingEmail($createCustomerDto->getEmail());
        $customer->setReference($createCustomerDto->getReference());
        $customer->setBillingAddress($address);

        $externalCustomerReference = $createCustomerDto->getExternalReference();

        if (isset($externalCustomerReference)) {
            $customer->setExternalCustomerReference($externalCustomerReference);
        }

        return $customer;
    }
}
