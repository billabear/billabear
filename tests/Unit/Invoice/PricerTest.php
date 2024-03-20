<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Invoice;

use App\Entity\Customer;
use App\Entity\Product;
use App\Invoice\Pricer;
use App\Tax\TaxInfo;
use App\Tax\TaxRateProviderInterface;
use Parthenon\Billing\Entity\Price;
use PHPUnit\Framework\TestCase;

class PricerTest extends TestCase
{
    public function testNullTax()
    {
        $price = new Price();
        $price->setAmount(1199);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer, $taxType)->willReturn(new TaxInfo(null, 'DE', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(0, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1199, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(null, $priceInfo->taxInfo->rate);
        $this->assertEquals(1199, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testGermanTax1199Inclusive()
    {
        $price = new Price();
        $price->setAmount(1199);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer, $taxType)->willReturn(new TaxInfo(19.0, 'DE', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(191, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1199, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(19, $priceInfo->taxInfo->rate);
        $this->assertEquals(1008, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testGermanTax1199Exclusive()
    {
        $price = new Price();
        $price->setAmount(1199);
        $price->setCurrency('EUR');
        $price->setIncludingTax(false);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(new TaxInfo(19.0, 'DE', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(228, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1427, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(19, $priceInfo->taxInfo->rate);
        $this->assertEquals(1199, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax12000Inclusive()
    {
        $price = new Price();
        $price->setAmount(12000);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer, $taxType)->willReturn(new TaxInfo(20.0, 'DE', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(2000, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(12000, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxInfo->rate);
        $this->assertEquals(10000, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax12000Exclusive()
    {
        $price = new Price();
        $price->setAmount(12000);
        $price->setCurrency('EUR');
        $price->setIncludingTax(false);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer, $taxType)->willReturn(new TaxInfo(20.0, 'GB', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(2400, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(14400.00, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxInfo->rate);
        $this->assertEquals(12000, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax122345Inclusive()
    {
        $price = new Price();
        $price->setAmount(22345);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(new TaxInfo(20.0, 'GB', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(3724, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(22345, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxInfo->rate);
        $this->assertEquals(18621, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax122345Exclusive()
    {
        $price = new Price();
        $price->setAmount(22345);
        $price->setCurrency('EUR');
        $price->setIncludingTax(false);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(new TaxInfo(20.0, 'GB', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(4469, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(26814, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxInfo->rate);
        $this->assertEquals(22345, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testChTax1234500Inclusive()
    {
        $price = new Price();
        $price->setAmount(1234500);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(new TaxInfo(7.5, 'CH', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(86128, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1234500, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(7.5, $priceInfo->taxInfo->rate);
        $this->assertEquals(1148372, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testChTax1234500Exclusive()
    {
        $price = new Price();
        $price->setAmount(1234500);
        $price->setCurrency('EUR');
        $price->setIncludingTax(false);
        $price->setProduct(new Product());

        $customer = new Customer();
        $taxType = new \App\Entity\TaxType();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(new TaxInfo(7.5, 'DE', false));

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer, $taxType);

        $this->assertEquals(92588, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1327088, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(7.5, $priceInfo->taxInfo->rate);
        $this->assertEquals(1234500, $priceInfo->subTotal->getMinorAmount()->toInt());
    }
}
