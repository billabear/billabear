<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Voucher;

use BillaBear\Credit\CreditAdjustmentRecorder;
use BillaBear\Entity\Credit;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Settings;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\Voucher;
use BillaBear\Entity\VoucherAmount;
use BillaBear\Entity\VoucherApplication;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\VoucherApplicationRepositoryInterface;
use BillaBear\Voucher\VoucherApplier;
use BillaBear\Voucher\VoucherType;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class VoucherApplierTest extends TestCase
{
    public function testApplyFixedCreditVoucherWithCustomerCreditCurrency(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $customer = $this->createMock(Customer::class);
        $customer->method('getCreditCurrency')->willReturn('USD');

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $money = Money::of(1000, 'USD');
        $voucherAmount->method('getAsMoney')->willReturn($money);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getAmountForCurrency')->with('USD')->willReturn($voucherAmount);
        $voucher->method('getName')->willReturn('Test Voucher');

        $creditAdjustmentRecorder->expects($this->once())
            ->method('createRecord')
            ->with(Credit::TYPE_CREDIT, $customer, $money, 'Test Voucher');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && true === $application->isUsed()
                    && $application->getCreatedAt() instanceof \DateTime;
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }

    public function testApplyFixedCreditVoucherWithSubscriptionCurrency(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $subscription = $this->createMock(Subscription::class);
        $subscription->method('isActive')->willReturn(true);
        $subscription->method('getCurrency')->willReturn('EUR');

        $customer = $this->createMock(Customer::class);
        $customer->method('getCreditCurrency')->willReturn(null);
        $customer->method('getSubscriptions')->willReturn(new ArrayCollection([$subscription]));

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $money = Money::of(500, 'EUR');
        $voucherAmount->method('getAsMoney')->willReturn($money);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getAmountForCurrency')->with('EUR')->willReturn($voucherAmount);
        $voucher->method('getName')->willReturn('Test Voucher');

        $creditAdjustmentRecorder->expects($this->once())
            ->method('createRecord')
            ->with(Credit::TYPE_CREDIT, $customer, $money, 'Test Voucher');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && true === $application->isUsed();
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }

    public function testApplyFixedCreditVoucherWithDefaultSystemCurrency(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $systemSettings = $this->createMock(SystemSettings::class);
        $systemSettings->method('getMainCurrency')->willReturn('GBP');

        $settings = $this->createMock(Settings::class);
        $settings->method('getSystemSettings')->willReturn($systemSettings);

        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $customer = $this->createMock(Customer::class);
        $customer->method('getCreditCurrency')->willReturn(null);
        $customer->method('getSubscriptions')->willReturn(new ArrayCollection([]));

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $money = Money::of(750, 'GBP');
        $voucherAmount->method('getAsMoney')->willReturn($money);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getAmountForCurrency')->with('GBP')->willReturn($voucherAmount);
        $voucher->method('getName')->willReturn('Test Voucher');

        $creditAdjustmentRecorder->expects($this->once())
            ->method('createRecord')
            ->with(Credit::TYPE_CREDIT, $customer, $money, 'Test Voucher');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && true === $application->isUsed();
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }

    public function testApplyFixedCreditVoucherSkipsInactiveSubscriptions(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $inactiveSubscription = $this->createMock(Subscription::class);
        $inactiveSubscription->method('isActive')->willReturn(false);
        $inactiveSubscription->method('getEndedAt')->willReturn(new \DateTime('-1 day'));
        $inactiveSubscription->method('getCurrency')->willReturn('USD');

        $activeSubscription = $this->createMock(Subscription::class);
        $activeSubscription->method('isActive')->willReturn(true);
        $activeSubscription->method('getCurrency')->willReturn('EUR');

        $customer = $this->createMock(Customer::class);
        $customer->method('getCreditCurrency')->willReturn(null);
        $customer->method('getSubscriptions')->willReturn(new ArrayCollection([$inactiveSubscription, $activeSubscription]));

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $money = Money::of(500, 'EUR');
        $voucherAmount->method('getAsMoney')->willReturn($money);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getAmountForCurrency')->with('EUR')->willReturn($voucherAmount);
        $voucher->method('getName')->willReturn('Test Voucher');

        $creditAdjustmentRecorder->expects($this->once())
            ->method('createRecord')
            ->with(Credit::TYPE_CREDIT, $customer, $money, 'Test Voucher');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && true === $application->isUsed();
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }

    public function testApplyPercentageVoucherDoesNotCreateCreditRecord(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $customer = $this->createMock(Customer::class);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::PERCENTAGE);

        $creditAdjustmentRecorder->expects($this->never())
            ->method('createRecord');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && false === $application->isUsed();
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }

    public function testApplyFixedCreditVoucherWithInactiveEndedSubscription(): void
    {
        $voucherApplicationRepository = $this->createMock(VoucherApplicationRepositoryInterface::class);
        $creditAdjustmentRecorder = $this->createMock(CreditAdjustmentRecorder::class);
        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);

        $systemSettings = $this->createMock(SystemSettings::class);
        $systemSettings->method('getMainCurrency')->willReturn('USD');

        $settings = $this->createMock(Settings::class);
        $settings->method('getSystemSettings')->willReturn($systemSettings);

        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $inactiveSubscription = $this->createMock(Subscription::class);
        $inactiveSubscription->method('isActive')->willReturn(false);
        $inactiveSubscription->method('getEndedAt')->willReturn(new \DateTime('-1 day'));

        $customer = $this->createMock(Customer::class);
        $customer->method('getCreditCurrency')->willReturn(null);
        $customer->method('getSubscriptions')->willReturn(new ArrayCollection([$inactiveSubscription]));

        $voucherAmount = $this->createMock(VoucherAmount::class);
        $money = Money::of(1000, 'USD');
        $voucherAmount->method('getAsMoney')->willReturn($money);

        $voucher = $this->createMock(Voucher::class);
        $voucher->method('getType')->willReturn(VoucherType::FIXED_CREDIT);
        $voucher->method('getAmountForCurrency')->with('USD')->willReturn($voucherAmount);
        $voucher->method('getName')->willReturn('Test Voucher');

        $creditAdjustmentRecorder->expects($this->once())
            ->method('createRecord')
            ->with(Credit::TYPE_CREDIT, $customer, $money, 'Test Voucher');

        $voucherApplicationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (VoucherApplication $application) use ($customer, $voucher) {
                return $application->getCustomer() === $customer
                    && $application->getVoucher() === $voucher
                    && true === $application->isUsed();
            }));

        $voucherApplier = new VoucherApplier(
            $voucherApplicationRepository,
            $creditAdjustmentRecorder,
            $settingsRepository
        );

        $voucherApplier->applyVoucherToCustomer($customer, $voucher);
    }
}
