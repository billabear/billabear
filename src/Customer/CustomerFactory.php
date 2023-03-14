<?php

namespace App\Customer;

use App\Dto\CreateCustomerDto;
use App\Dto\Generic\Address as AddressDto;
use App\Dto\Generic\Customer as CustomerDto;
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
        $customer->setName($createCustomerDto->getName());

        $externalCustomerReference = $createCustomerDto->getExternalReference();

        if (isset($externalCustomerReference)) {
            $customer->setExternalCustomerReference($externalCustomerReference);
        }

        return $customer;
    }

    public function createDtoFromCustomer(Customer $customer): CustomerDto
    {
        $address = new AddressDto();
        $address->setStreetLineOne($customer->getBillingAddress()->getStreetLineOne());
        $address->setStreetLineTwo($customer->getBillingAddress()->getStreetLineTwo());
        $address->setCity($customer->getBillingAddress()->getCity());
        $address->setRegion($customer->getBillingAddress()->getRegion());
        $address->setCountry($customer->getBillingAddress()->getCountry());
        $address->setPostcode($customer->getBillingAddress()->getPostcode());

        $dto = new CustomerDto();
        $dto->setName($customer->getName());
        $dto->setId((string) $customer->getId());
        $dto->setReference($customer->getReference());
        $dto->setEmail($customer->getBillingEmail());
        $dto->setExternalReference($customer->getExternalCustomerReference());
        $dto->setAddress($address);

        return $dto;
    }
}
