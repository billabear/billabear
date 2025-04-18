<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription\Schedule;

use BillaBear\Repository\SettingsRepositoryInterface;
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
