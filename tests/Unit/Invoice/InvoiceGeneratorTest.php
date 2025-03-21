<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Invoice;

use BillaBear\Credit\CreditAdjustmentRecorder;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\Price;
use BillaBear\Entity\Product;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use BillaBear\Invoice\DueDateDecider;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Invoice\Number\InvoiceNumberGeneratorInterface;
use BillaBear\Invoice\Number\InvoiceNumberGeneratorProvider;
use BillaBear\Invoice\QuantityProvider;
use BillaBear\Payment\ExchangeRates\BricksExchangeRateProvider;
use BillaBear\Payment\ExchangeRates\ToSystemConverter;
use BillaBear\Pricing\PriceInfo;
use BillaBear\Pricing\Pricer;
use BillaBear\Pricing\Usage\MetricProvider;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\Usage\MetricCounterRepositoryInterface;
use BillaBear\Repository\VoucherApplicationRepositoryInterface;
use BillaBear\Tax\TaxInfo;
use Brick\Money\Money;
use Monolog\Test\TestCase;
use Parthenon\Common\Exception\NoEntityFoundException;

class InvoiceGeneratorTest extends TestCase
{
    public function testCreateInvoiceFromSubscriptions()
    {
        $mockPrice = $this->createMock(Price::class);
        $taxType = $this->createMock(\BillaBear\Entity\TaxType::class);

        $product = $this->createMock(Product::class);
        $product->method('getTaxType')->willReturn($taxType);

        $subscriptionPlan = $this->createMock(SubscriptionPlan::class);
        $subscriptionPlan->method('getProduct')->willReturn($product);

        $subscriptionOne = $this->createMock(Subscription::class);
        $subscriptionOne->method('getPrice')->willReturn($mockPrice);
        $subscriptionOne->method('getPlanName')->willReturn('Plan Name One');
        $subscriptionOne->method('getSubscriptionPlan')->willReturn($subscriptionPlan);
        $subscriptionOne->method('getSeats')->willReturn(1);

        $subscriptionTwo = $this->createMock(Subscription::class);
        $subscriptionTwo->method('getPrice')->willReturn($mockPrice);
        $subscriptionTwo->method('getPlanName')->willReturn('Plan Name Two');
        $subscriptionTwo->method('getSubscriptionPlan')->willReturn($subscriptionPlan);
        $subscriptionTwo->method('getSeats')->willReturn(1);

        $customer = $this->createMock(Customer::class);
        $invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $invoiceNumberGenerator->method('generate')->willReturn('D7-848484');

        $invoiceNumberGeneratorProvider = $this->createMock(InvoiceNumberGeneratorProvider::class);
        $invoiceNumberGeneratorProvider->method('getGenerator')->willReturn($invoiceNumberGenerator);

        $priceInfoOne = new PriceInfo(Money::ofMinor(1000, 'USD'), Money::ofMinor(800, 'USD'), Money::ofMinor(200, 'USD'), new TaxInfo(20.0, 'de', false), 1, Money::ofMinor(3200, 'USD'));
        $priceInfoTwo = new PriceInfo(Money::ofMinor(4000, 'USD'), Money::ofMinor(3200, 'USD'), Money::ofMinor(800, 'USD'), new TaxInfo(20.0, 'de', false), 1, Money::ofMinor(3200, 'USD'));

        $pricer = $this->createMock(Pricer::class);
        $pricer->method('getCustomerPriceInfo')->willReturnOnConsecutiveCalls([$priceInfoOne], [$priceInfoTwo]);

        $repository = $this->createMock(InvoiceRepositoryInterface::class);
        $repository->expects($this->once())->method('save')->with($this->isInstanceOf(Invoice::class));

        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);

        $voucherApplication = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $voucherApplication->method('findUnUsedForCustomer')->willThrowException(new NoEntityFoundException());

        $eventDispatcher = $this->createMock(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class);

        $dueDateDecider = $this->createMock(DueDateDecider::class);
        $exchangeRateProvider = $this->createMock(BricksExchangeRateProvider::class);

        $metricProvider = $this->createMock(MetricProvider::class);
        $metricUsage = $this->createMock(MetricCounterRepositoryInterface::class);

        $quantityProvider = $this->createMock(QuantityProvider::class);
        $quantityProvider->method('getQuantity')->willReturn(1);

        $toSystemConverter = $this->createMock(ToSystemConverter::class);
        $toSystemConverter->method('convert')->willReturnArgument(0);

        $subject = new InvoiceGenerator(
            $pricer,
            $invoiceNumberGeneratorProvider,
            $repository,
            $creditAdjustmentRecorder,
            $voucherApplication,
            $eventDispatcher,
            $dueDateDecider,
            $metricProvider,
            $metricUsage,
            $quantityProvider,
            $toSystemConverter,
            $exchangeRateProvider,
        );
        $actual = $subject->generateForCustomerAndSubscriptions($customer, [$subscriptionOne, $subscriptionTwo]);

        $this->assertCount(2, $actual->getLines());
        $this->assertEquals(5000, $actual->getTotal());
        $this->assertEquals(4000, $actual->getSubTotal());
        $this->assertEquals(1000, $actual->getTaxTotal());
    }
}
