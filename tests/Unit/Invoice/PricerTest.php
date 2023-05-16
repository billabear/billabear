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

namespace App\Tests\Unit\Invoice;

use App\Entity\Customer;
use App\Invoice\Pricer;
use App\Tax\TaxRateProviderInterface;
use Parthenon\Billing\Entity\Price;
use PHPUnit\Framework\TestCase;

class PricerTest extends TestCase
{
    public function testGermanTax1199Inclusive()
    {
        $price = new Price();
        $price->setAmount(1199);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);

        $customer = new Customer();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(19.0);

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer);

        $this->assertEquals(191, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1199, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(19, $priceInfo->taxRate);
        $this->assertEquals(1008, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax12000Inclusive()
    {
        $price = new Price();
        $price->setAmount(12000);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);

        $customer = new Customer();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(20.0);

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer);

        $this->assertEquals(2000, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(12000, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxRate);
        $this->assertEquals(10000, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testUkTax122345Inclusive()
    {
        $price = new Price();
        $price->setAmount(22345);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);

        $customer = new Customer();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(20.0);

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer);

        $this->assertEquals(3724, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(22345, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(20, $priceInfo->taxRate);
        $this->assertEquals(18621, $priceInfo->subTotal->getMinorAmount()->toInt());
    }

    public function testChTax1234500Inclusive()
    {
        $price = new Price();
        $price->setAmount(1234500);
        $price->setCurrency('EUR');
        $price->setIncludingTax(true);

        $customer = new Customer();

        $taxProvider = $this->createMock(TaxRateProviderInterface::class);
        $taxProvider->method('getRateForCustomer')->with($customer)->willReturn(7.5);

        $subject = new Pricer($taxProvider);
        $priceInfo = $subject->getCustomerPriceInfo($price, $customer);

        $this->assertEquals(86128, $priceInfo->vat->getMinorAmount()->toInt());
        $this->assertEquals(1234500, $priceInfo->total->getMinorAmount()->toInt());
        $this->assertEquals(7.5, $priceInfo->taxRate);
        $this->assertEquals(1148372, $priceInfo->subTotal->getMinorAmount()->toInt());
    }
}
