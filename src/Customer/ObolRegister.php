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

        $customer->setExternalCustomerReference($creation->getReference());
        $customer->setPaymentProviderDetailsUrl($creation->getDetailsUrl());

        return $customer;
    }
}
