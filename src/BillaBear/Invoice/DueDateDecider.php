<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class DueDateDecider
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    public function setDueAt(Invoice $invoice): void
    {
        $databaseDueTime = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getDefaultInvoiceDueTime();

        try {
            $date = new \DateTime('+'.$databaseDueTime);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Using default due time', ['failed_due_time' => $databaseDueTime, 'exception' => $e->getMessage()]);
            $date = new \DateTime('+'.SystemSettings::DEFAULT_DUE_TIME);
        }

        $invoice->setDueAt($date);
    }
}
