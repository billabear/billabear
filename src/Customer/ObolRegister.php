<?php

namespace App\Customer;

use App\Entity\Customer;
use Obol\Model\Address;
use Obol\Model\Customer as ObolCustomer;
use Obol\Provider\ProviderInterface;

class ObolRegister implements ExternalRegisterInterface
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    public function register(Customer $customer): Customer
    {
        $address = new Address();
        $address->setCountryCode($customer->getBillingAddress()->getCountry());

        $obolCustomer = new ObolCustomer();
        $obolCustomer->setEmail($customer->getBillingEmail());
        $obolCustomer->setDescription($customer->getReference());
        $obolCustomer->setAddress($address);

        $creation = $this->provider->customers()->create($obolCustomer);

        $customer->setExternalCustomerReference($creation->getId());

        return $customer;
    }
}
