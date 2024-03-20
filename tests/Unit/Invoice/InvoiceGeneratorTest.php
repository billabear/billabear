<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Unit\Invoice;

use App\Credit\CreditAdjustmentRecorder;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\Product;
use App\Entity\SubscriptionPlan;
use App\Invoice\DueDateDecider;
use App\Invoice\InvoiceGenerator;
use App\Invoice\Number\InvoiceNumberGeneratorInterface;
use App\Invoice\Number\InvoiceNumberGeneratorProvider;
use App\Invoice\PriceInfo;
use App\Invoice\Pricer;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\VoucherApplicationRepositoryInterface;
use App\Tax\TaxInfo;
use Brick\Money\Money;
use Monolog\Test\TestCase;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;

class InvoiceGeneratorTest extends TestCase
{
    public function testCreateInvoiceFromSubscriptions()
    {
        $mockPrice = $this->createMock(Price::class);
        $taxType = $this->createMock(\App\Entity\TaxType::class);

        $product = $this->createMock(Product::class);
        $product->method('getTaxType')->willReturn($taxType);

        $subscriptionPlan = $this->createMock(SubscriptionPlan::class);
        $subscriptionPlan->method('getProduct')->willReturn($product);

        $subscriptionOne = $this->createMock(Subscription::class);
        $subscriptionOne->method('getPrice')->willReturn($mockPrice);
        $subscriptionOne->method('getPlanName')->willReturn('Plan Name One');
        $subscriptionOne->method('getSubscriptionPlan')->willReturn($subscriptionPlan);

        $subscriptionTwo = $this->createMock(Subscription::class);
        $subscriptionTwo->method('getPrice')->willReturn($mockPrice);
        $subscriptionTwo->method('getPlanName')->willReturn('Plan Name Two');
        $subscriptionTwo->method('getSubscriptionPlan')->willReturn($subscriptionPlan);

        $customer = $this->createMock(Customer::class);
        $invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $invoiceNumberGenerator->method('generate')->willReturn('D7-848484');

        $invoiceNumberGeneratorProvider = $this->createMock(InvoiceNumberGeneratorProvider::class);
        $invoiceNumberGeneratorProvider->method('getGenerator')->willReturn($invoiceNumberGenerator);

        $priceInfoOne = new PriceInfo(Money::ofMinor(1000, 'USD'), Money::ofMinor(800, 'USD'), Money::ofMinor(200, 'USD'), new TaxInfo(20.0, 'de', false));
        $priceInfoTwo = new PriceInfo(Money::ofMinor(4000, 'USD'), Money::ofMinor(3200, 'USD'), Money::ofMinor(800, 'USD'), new TaxInfo(20.0, 'de', false));

        $pricer = $this->createMock(Pricer::class);
        $pricer->method('getCustomerPriceInfo')->willReturnOnConsecutiveCalls($priceInfoOne, $priceInfoTwo);

        $repository = $this->createMock(InvoiceRepositoryInterface::class);
        $repository->expects($this->once())->method('save')->with($this->isInstanceOf(Invoice::class));

        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);

        $voucherApplication = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $voucherApplication->method('findUnUsedForCustomer')->willThrowException(new NoEntityFoundException());

        $eventDispatcher = $this->createMock(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class);

        $dueDateDecider = $this->createMock(DueDateDecider::class);

        $subject = new InvoiceGenerator($pricer, $invoiceNumberGeneratorProvider, $repository, $creditAdjustmentRecorder, $voucherApplication, $eventDispatcher, $dueDateDecider);
        $actual = $subject->generateForCustomerAndSubscriptions($customer, [$subscriptionOne, $subscriptionTwo]);

        $this->assertCount(2, $actual->getLines());
        $this->assertEquals(5000, $actual->getTotal());
        $this->assertEquals(4000, $actual->getSubTotal());
        $this->assertEquals(1000, $actual->getTaxTotal());
    }
}
