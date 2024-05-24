<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Tax;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Product;
use BillaBear\Entity\TaxType;
use BillaBear\Tax\UeberTaxRateProvider;
use Parthenon\Common\Address;
use PHPUnit\Framework\TestCase;

class TaxRateProviderTest extends TestCase
{
    public function testCustomerHasTaxRate()
    {
        $subject = new UeberTaxRateProvider();

        $product = new Product();
        $product->setTaxRate(15);

        $taxType = new TaxType();

        $brandingSettings = new BrandSettings();
        $brandingSettings->setAddress(new Address());
        $brandingSettings->getAddress()->setCountry('DE');

        $customers = new Customer();
        $customers->setBrandSettings($brandingSettings);
        $customers->setStandardTaxRate(13);
        $customers->setBillingAddress(new Address());
        $customers->getBillingAddress()->setCountry('GB');

        $actual = $subject->getRateForCustomer($customers, $taxType, $product);

        $this->assertEquals(13, $actual->rate);
        $this->assertEquals('GB', $actual->country);
    }

    public function testProductHasTaxRate()
    {
        $subject = new UeberTaxRateProvider();

        $product = new Product();
        $product->setTaxRate(15);

        $taxType = new TaxType();

        $brandingSettings = new BrandSettings();
        $brandingSettings->setAddress(new Address());
        $brandingSettings->getAddress()->setCountry('DE');

        $customers = new Customer();
        $customers->setBrandSettings($brandingSettings);
        $customers->setBillingAddress(new Address());
        $customers->getBillingAddress()->setCountry('GB');

        $actual = $subject->getRateForCustomer($customers, $taxType, $product);

        $this->assertEquals(15, $actual->rate);
        $this->assertEquals('DE', $actual->country);
    }

    public function testBrandHasTaxRate()
    {
        $subject = new UeberTaxRateProvider();

        $product = new Product();

        $taxType = new TaxType();

        $brandingSettings = new BrandSettings();
        $brandingSettings->setAddress(new Address());
        $brandingSettings->getAddress()->setCountry('DE');
        $brandingSettings->setTaxRate(15.8);

        $customers = new Customer();
        $customers->setBrandSettings($brandingSettings);
        $customers->setBillingAddress(new Address());
        $customers->getBillingAddress()->setCountry('GB');

        $actual = $subject->getRateForCustomer($customers, $taxType, $product);

        $this->assertEquals(15.8, $actual->rate);
        $this->assertEquals('DE', $actual->country);
    }
}
