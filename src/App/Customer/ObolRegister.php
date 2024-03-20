<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Customer;

use App\Entity\Customer;
use App\Repository\SettingsRepositoryInterface;
use Obol\Model\Address;
use Obol\Model\Customer as ObolCustomer;
use Obol\Provider\ProviderInterface;

class ObolRegister implements ExternalRegisterInterface
{
    public function __construct(private ProviderInterface $provider, private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function register(Customer $customer): Customer
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $taxExempt = !$settings->getTaxSettings()->getTaxCustomersWithTaxNumbers() && !empty($customer->getTaxNumber());
        $address = new Address();
        $address->setCountryCode($customer->getBillingAddress()->getCountry());

        $obolCustomer = new ObolCustomer();
        $obolCustomer->setEmail($customer->getBillingEmail());
        $obolCustomer->setDescription($customer->getReference());
        $obolCustomer->setAddress($address);
        $obolCustomer->setTaxExempt($taxExempt);

        $creation = $this->provider->customers()->create($obolCustomer);

        $customer->setExternalCustomerReference($creation->getReference());
        $customer->setPaymentProviderDetailsUrl($creation->getDetailsUrl());

        return $customer;
    }

    public function update(Customer $customer): Customer
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $taxExempt = !$settings->getTaxSettings()->getTaxCustomersWithTaxNumbers() && !empty($customer->getTaxNumber());
        $address = new Address();
        $address->setCountryCode($customer->getBillingAddress()->getCountry());

        $obolCustomer = new ObolCustomer();
        $obolCustomer->setId($customer->getExternalCustomerReference());
        $obolCustomer->setEmail($customer->getBillingEmail());
        $obolCustomer->setDescription($customer->getReference());
        $obolCustomer->setAddress($address);
        $obolCustomer->setTaxExempt($taxExempt);

        $this->provider->customers()->update($obolCustomer);

        return $customer;
    }
}
