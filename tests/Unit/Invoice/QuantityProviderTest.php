<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Invoice;

use BillaBear\Entity\Settings;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Invoice\InvoiceGenerationType;
use BillaBear\Invoice\QuantityProvider;
use BillaBear\Repository\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;

class QuantityProviderTest extends TestCase
{
    public function testReturnsQuantityWhenPeriodic()
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::PERIODICALLY);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $quantityProvider = new QuantityProvider($settingsRepository);

        $this->assertEquals(31, $quantityProvider->getQuantity(31, new \DateTime(), new \BillaBear\Entity\Subscription()));
    }

    public function testReturnsQuantityWhenOneWhenOneDayLeft()
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::END_OF_MONTH);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $quantityProvider = new QuantityProvider($settingsRepository);
        $subscription = new \BillaBear\Entity\Subscription();
        $subscription->setValidUntil(new \DateTime('2024-01-31'));

        $this->assertEquals(1, $quantityProvider->getQuantity(31, new \DateTime('2024-01-30'), $subscription));
    }

    public function testReturnsQuantityWhen01WhenTenDayLeft()
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::END_OF_MONTH);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $quantityProvider = new QuantityProvider($settingsRepository);
        $subscription = new \BillaBear\Entity\Subscription();
        $subscription->setValidUntil(new \DateTime('2024-04-30'));

        $this->assertEquals(0.33, $quantityProvider->getQuantity(1, new \DateTime('2024-04-20'), $subscription));
    }

    public function testReturnsQuantityWhen01WhenTenDayLeftOddTime()
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::END_OF_MONTH);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $quantityProvider = new QuantityProvider($settingsRepository);
        $subscription = new \BillaBear\Entity\Subscription();
        $subscription->setValidUntil(new \DateTime('2024-12-31 13:33:21'));

        $this->assertEquals(0.03, $quantityProvider->getQuantity(1, new \DateTime('2024-12-30 13:33:21'), $subscription));
    }

    public function testReturnsQuantityWhenThatDay()
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::END_OF_MONTH);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        $quantityProvider = new QuantityProvider($settingsRepository);
        $subscription = new \BillaBear\Entity\Subscription();
        $subscription->setValidUntil(new \DateTime('2024-12-31 23:33:21'));

        $this->assertEquals(0.02, $quantityProvider->getQuantity(1, new \DateTime('2024-12-31 13:33:21'), $subscription));
    }
}
