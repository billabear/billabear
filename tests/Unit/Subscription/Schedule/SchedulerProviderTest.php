<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Subscription\Schedule;

use BillaBear\Entity\Settings;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Invoice\InvoiceGenerationType;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Subscription\Schedule\MonthScheduler;
use BillaBear\Subscription\Schedule\SchedulerProvider;
use BillaBear\Subscription\Schedule\WeekScheduler;
use BillaBear\Subscription\Schedule\YearScheduler;
use Parthenon\Billing\Entity\Price;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchedulerProviderTest extends TestCase
{
    public function testReturnWeekScheduler()
    {
        $price = new Price();
        $price->setSchedule('week');
        $settings = $this->buildSettings();
        $subject = new SchedulerProvider($settings);
        $this->assertInstanceOf(WeekScheduler::class, $subject->getScheduler($price));
    }

    public function testReturnMonthScheduler()
    {
        $price = new Price();
        $price->setSchedule('month');
        $settings = $this->buildSettings();
        $subject = new SchedulerProvider($settings);
        $this->assertInstanceOf(MonthScheduler::class, $subject->getScheduler($price));
    }

    public function testReturnYearScheduler()
    {
        $price = new Price();
        $price->setSchedule('year');
        $settings = $this->buildSettings();
        $subject = new SchedulerProvider($settings);
        $this->assertInstanceOf(YearScheduler::class, $subject->getScheduler($price));
    }

    public function buildSettings(): MockObject&SettingsRepositoryInterface
    {
        $systemSettings = new SystemSettings();
        $systemSettings->setInvoiceGenerationType(InvoiceGenerationType::PERIODICALLY);

        $settings = new Settings();
        $settings->setSystemSettings($systemSettings);

        $settingsRepository = $this->createMock(SettingsRepositoryInterface::class);
        $settingsRepository->method('getDefaultSettings')->willReturn($settings);

        return $settingsRepository;
    }
}
