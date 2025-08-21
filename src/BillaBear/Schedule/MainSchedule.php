<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Schedule;

use BillaBear\Schedule\Messenger\Message\BeforeChargeWarning;
use BillaBear\Schedule\Messenger\Message\CheckIfInvoicesPaid;
use BillaBear\Schedule\Messenger\Message\CounterUpdate;
use BillaBear\Schedule\Messenger\Message\DisableOverdueCustomers;
use BillaBear\Schedule\Messenger\Message\ExpiredCardsDayBefore;
use BillaBear\Schedule\Messenger\Message\ExpiredCardsFirstOfMonth;
use BillaBear\Schedule\Messenger\Message\GenerateNewInvoices;
use BillaBear\Schedule\Messenger\Message\GenericTasks;
use BillaBear\Schedule\Messenger\Message\InvoiceOverdueWarning;
use BillaBear\Schedule\Messenger\Message\MassSubscriptionChange;
use BillaBear\Schedule\Messenger\Message\RefreshExchangeRates;
use BillaBear\Schedule\Messenger\Message\RetryPayments;
use BillaBear\Schedule\Messenger\Message\StripeImport;
use BillaBear\Schedule\Messenger\Message\SyncEstimates;
use BillaBear\Schedule\Messenger\Message\TrialEndingWarning;
use BillaBear\Schedule\Messenger\Message\UpdateChecker;
use BillaBear\Schedule\Messenger\Message\VatSenseSync;
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
        $schedule->add(RecurringMessage::cron('* * * * *', new CounterUpdate()));
        $schedule->add(RecurringMessage::cron('*/2 * * * *', new SyncEstimates()));
        $schedule->add(RecurringMessage::cron('15 2 * * *', new VatSenseSync()));
        $schedule->add(RecurringMessage::cron('1 3 * * *', new RefreshExchangeRates())); // Every day at 03:01 - this avoids the standard midnight process rush.
        $schedule->add(RecurringMessage::cron('*/5 * * * *', new GenerateNewInvoices()));
        $schedule->add(RecurringMessage::cron('5 0 * * *', new ExpiredCardsDayBefore()));
        $schedule->add(RecurringMessage::cron('1 0 1 * *', new ExpiredCardsFirstOfMonth()));
        $schedule->add(RecurringMessage::cron('5 1 * * *', new BeforeChargeWarning()));
        $schedule->add(RecurringMessage::cron('15 1 * * *', new TrialEndingWarning()));
        $schedule->add(RecurringMessage::cron('1 2 * * *', new UpdateChecker()));
        $schedule->add(RecurringMessage::cron('1 3 * * *', new InvoiceOverdueWarning()));
        $schedule->add(RecurringMessage::cron('1 4 * * *', new DisableOverdueCustomers()));
        $schedule->add(RecurringMessage::cron('*/5 * * * *', new MassSubscriptionChange()));
        $schedule->add(RecurringMessage::cron('0 */12 * * *', new CheckIfInvoicesPaid()));

        return $schedule;
    }
}
