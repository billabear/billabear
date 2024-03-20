<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Schedule;

use App\Schedule\Messenger\Message\DisableOverdueCustomers;
use App\Schedule\Messenger\Message\ExpiredCardsDayBefore;
use App\Schedule\Messenger\Message\ExpiredCardsFirstOfMonth;
use App\Schedule\Messenger\Message\GenerateNewInvoices;
use App\Schedule\Messenger\Message\GenericTasks;
use App\Schedule\Messenger\Message\InvoiceOverdueWarning;
use App\Schedule\Messenger\Message\MassSubscriptionChange;
use App\Schedule\Messenger\Message\RefreshExchangeRates;
use App\Schedule\Messenger\Message\RetryPayments;
use App\Schedule\Messenger\Message\StripeImport;
use App\Schedule\Messenger\Message\UpdateChecker;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('main')]
class MainSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();
        $schedule->add(RecurringMessage::cron('* * * * *', new StripeImport()));
        $schedule->add(RecurringMessage::cron('* * * * *', new GenericTasks()));
        $schedule->add(RecurringMessage::cron('* * * * *', new RetryPayments()));
        $schedule->add(RecurringMessage::cron('1 3 * * *', new RefreshExchangeRates())); // Every day at 03:01 - this avoids the standard midnight process rush.
        $schedule->add(RecurringMessage::cron('*/5 * * * *', new GenerateNewInvoices()));
        $schedule->add(RecurringMessage::cron('5 0 * * *', new ExpiredCardsDayBefore()));
        $schedule->add(RecurringMessage::cron('1 0 1 * *', new ExpiredCardsFirstOfMonth()));
        $schedule->add(RecurringMessage::cron('1 2 * * *', new UpdateChecker()));
        $schedule->add(RecurringMessage::cron('1 3 * * *', new InvoiceOverdueWarning()));
        $schedule->add(RecurringMessage::cron('1 4 * * *', new DisableOverdueCustomers()));
        $schedule->add(RecurringMessage::cron('*/5 * * * *', new MassSubscriptionChange()));

        return $schedule;
    }
}
