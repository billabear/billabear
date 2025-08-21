<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Voucher;

use BillaBear\Entity\Voucher;
use BillaBear\Entity\VoucherAmount;
use BillaBear\Voucher\VoucherRegister;
use BillaBear\Voucher\VoucherType;
use Doctrine\Common\Collections\ArrayCollection;
use Obol\Model\Voucher\Amount;
use Obol\Model\Voucher\Voucher as ObolVoucher;
use Obol\Model\Voucher\VoucherCreation;
use Obol\Provider\ProviderInterface;
use Obol\VoucherServiceInterface;
use PHPUnit\Framework\TestCase;

class VoucherRegisterTest extends TestCase
{
    public function testRegisterFixedCreditVoucher(): void
    {
        $voucherService = $this->createMock(VoucherServiceInterface::class);
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('vouchers')->willReturn($voucherService);

        $voucherAmount1 = $this->createMock(VoucherAmount::class);
        $voucherAmount1->method('getAmount')->willReturn(1000);
        $voucherAmount1->method('getCurrency')->willReturn('USD');

        $voucherAmount2 = $this->createMock(VoucherAmount::class);
        $voucherAmount2->method('getAmount')->willReturn(500);
        $voucherAmount2->method('getCurrency')->willReturn('EUR');

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getPercentage')->willReturn(null);
        $voucher->method('getCode')->willReturn('TEST123');
        $voucher->method('getName')->willReturn('Test Voucher');
        $voucher->method('getAmounts')->willReturn(new ArrayCollection([$voucherAmount1, $voucherAmount2]));

        $voucherCreation = $this->createMock(VoucherCreation::class);
        $voucherCreation->method('getId')->willReturn('external-voucher-id');

        $voucherService->expects($this->once())
            ->method('createVoucher')
            ->with($this->callback(function (ObolVoucher $obolVoucher) {
                return 'fixed_credit' === $obolVoucher->getType()
                    && null === $obolVoucher->getPercentage()
                    && 'once' === $obolVoucher->getDuration()
                    && 'TEST123' === $obolVoucher->getCode()
                    && 'Test Voucher' === $obolVoucher->getName()
                    && 2 === count($obolVoucher->getAmounts());
            }))
            ->willReturn($voucherCreation);

        $voucher->expects($this->once())
            ->method('setExternalReference')
            ->with('external-voucher-id');

        $voucherRegister = new VoucherRegister($provider);
        $voucherRegister->register($voucher);
    }

    public function testRegisterPercentageVoucher(): void
    {
        $voucherService = $this->createMock(VoucherServiceInterface::class);
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('vouchers')->willReturn($voucherService);

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $voucherAmount->method('getAmount')->willReturn(0);
        $voucherAmount->method('getCurrency')->willReturn('USD');

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::PERCENTAGE);
        $voucher->method('getPercentage')->willReturn(25);
        $voucher->method('getCode')->willReturn('PERCENT25');
        $voucher->method('getName')->willReturn('25% Off Voucher');
        $voucher->method('getAmounts')->willReturn(new ArrayCollection([$voucherAmount]));

        $voucherCreation = $this->createMock(VoucherCreation::class);
        $voucherCreation->method('getId')->willReturn('external-percentage-voucher-id');

        $voucherService->expects($this->once())
            ->method('createVoucher')
            ->with($this->callback(function (ObolVoucher $obolVoucher) {
                return 'percentage' === $obolVoucher->getType()
                    && 25 === $obolVoucher->getPercentage()
                    && 'once' === $obolVoucher->getDuration()
                    && 'PERCENT25' === $obolVoucher->getCode()
                    && '25% Off Voucher' === $obolVoucher->getName()
                    && 1 === count($obolVoucher->getAmounts());
            }))
            ->willReturn($voucherCreation);

        $voucher->expects($this->once())
            ->method('setExternalReference')
            ->with('external-percentage-voucher-id');

        $voucherRegister = new VoucherRegister($provider);
        $voucherRegister->register($voucher);
    }

    public function testRegisterVoucherWithMultipleAmounts(): void
    {
        $voucherService = $this->createMock(VoucherServiceInterface::class);
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('vouchers')->willReturn($voucherService);

        $voucherAmount1 = $this->createMock(VoucherAmount::class);
        $voucherAmount1->method('getAmount')->willReturn(1000);
        $voucherAmount1->method('getCurrency')->willReturn('USD');

        $voucherAmount2 = $this->createMock(VoucherAmount::class);
        $voucherAmount2->method('getAmount')->willReturn(750);
        $voucherAmount2->method('getCurrency')->willReturn('EUR');

        $voucherAmount3 = $this->createMock(VoucherAmount::class);
        $voucherAmount3->method('getAmount')->willReturn(850);
        $voucherAmount3->method('getCurrency')->willReturn('GBP');

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getPercentage')->willReturn(null);
        $voucher->method('getCode')->willReturn('MULTI123');
        $voucher->method('getName')->willReturn('Multi Currency Voucher');
        $voucher->method('getAmounts')->willReturn(new ArrayCollection([$voucherAmount1, $voucherAmount2, $voucherAmount3]));

        $voucherCreation = $this->createMock(VoucherCreation::class);
        $voucherCreation->method('getId')->willReturn('external-multi-voucher-id');

        $voucherService->expects($this->once())
            ->method('createVoucher')
            ->with($this->callback(function (ObolVoucher $obolVoucher) {
                $amounts = $obolVoucher->getAmounts();

                return 'fixed_credit' === $obolVoucher->getType()
                    && 'MULTI123' === $obolVoucher->getCode()
                    && 'Multi Currency Voucher' === $obolVoucher->getName()
                    && 3 === count($amounts)
                    && 1000 === $amounts[0]->getAmount()
                    && 'USD' === $amounts[0]->getCurrency()
                    && 750 === $amounts[1]->getAmount()
                    && 'EUR' === $amounts[1]->getCurrency()
                    && 850 === $amounts[2]->getAmount()
                    && 'GBP' === $amounts[2]->getCurrency();
            }))
            ->willReturn($voucherCreation);

        $voucher->expects($this->once())
            ->method('setExternalReference')
            ->with('external-multi-voucher-id');

        $voucherRegister = new VoucherRegister($provider);
        $voucherRegister->register($voucher);
    }

    public function testRegisterVoucherWithEmptyAmounts(): void
    {
        $voucherService = $this->createMock(VoucherServiceInterface::class);
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('vouchers')->willReturn($voucherService);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::PERCENTAGE);
        $voucher->method('getPercentage')->willReturn(10);
        $voucher->method('getCode')->willReturn('EMPTY123');
        $voucher->method('getName')->willReturn('Empty Amounts Voucher');
        $voucher->method('getAmounts')->willReturn(new ArrayCollection([]));

        $voucherCreation = $this->createMock(VoucherCreation::class);
        $voucherCreation->method('getId')->willReturn('external-empty-voucher-id');

        $voucherService->expects($this->once())
            ->method('createVoucher')
            ->with($this->callback(function (ObolVoucher $obolVoucher) {
                return 'percentage' === $obolVoucher->getType()
                    && 10 === $obolVoucher->getPercentage()
                    && 'once' === $obolVoucher->getDuration()
                    && 'EMPTY123' === $obolVoucher->getCode()
                    && 'Empty Amounts Voucher' === $obolVoucher->getName()
                    && 0 === count($obolVoucher->getAmounts());
            }))
            ->willReturn($voucherCreation);

        $voucher->expects($this->once())
            ->method('setExternalReference')
            ->with('external-empty-voucher-id');

        $voucherRegister = new VoucherRegister($provider);
        $voucherRegister->register($voucher);
    }

    public function testRegisterVoucherSetsCorrectObolAmountProperties(): void
    {
        $voucherService = $this->createMock(VoucherServiceInterface::class);
        $provider = $this->createMock(ProviderInterface::class);
        $provider->method('vouchers')->willReturn($voucherService);

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $voucherAmount->method('getAmount')->willReturn(2500);
        $voucherAmount->method('getCurrency')->willReturn('CAD');

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getPercentage')->willReturn(null);
        $voucher->method('getCode')->willReturn('CAD123');
        $voucher->method('getName')->willReturn('CAD Voucher');
        $voucher->method('getAmounts')->willReturn(new ArrayCollection([$voucherAmount]));

        $voucherCreation = $this->createMock(VoucherCreation::class);
        $voucherCreation->method('getId')->willReturn('external-cad-voucher-id');

        $voucherService->expects($this->once())
            ->method('createVoucher')
            ->with($this->callback(function (ObolVoucher $obolVoucher) {
                $amounts = $obolVoucher->getAmounts();
                $amount = $amounts[0];

                return $amount instanceof Amount
                    && 2500 === $amount->getAmount()
                    && 'CAD' === $amount->getCurrency();
            }))
            ->willReturn($voucherCreation);

        $voucher->expects($this->once())
            ->method('setExternalReference')
            ->with('external-cad-voucher-id');

        $voucherRegister = new VoucherRegister($provider);
        $voucherRegister->register($voucher);
    }
}
