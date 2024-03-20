<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Subscription\Schedule;

use App\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Subscription\ObolScheduler;

class SchedulerHandler implements \Parthenon\Billing\Subscription\SchedulerInterface
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository, private ObolScheduler $obolScheduler)
    {
    }

    public function scheduleNextCharge(Subscription $subscription): void
    {
        if ($this->settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling()) {
            $this->obolScheduler->scheduleNextCharge($subscription);
        }
    }
}
