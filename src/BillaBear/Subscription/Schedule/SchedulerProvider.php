<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Schedule;

use BillaBear\Invoice\InvoiceGenerationType;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Entity\Price;

class SchedulerProvider
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function getScheduler(Price $price): SchedulerInterface
    {
        if ('week' === $price->getSchedule()) {
            return new WeekScheduler();
        }

        if ('month' === $price->getSchedule()) {
            $settings = $this->settingsRepository->getDefaultSettings();

            if (InvoiceGenerationType::END_OF_MONTH === $settings->getSystemSettings()->getInvoiceGenerationType()) {
                return new EndOfMonthScheduler();
            }

            return new MonthScheduler();
        }

        if ('year' === $price->getSchedule()) {
            return new YearScheduler();
        }

        return new OneOffScheduler();
    }
}
