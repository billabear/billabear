<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Invoice;

use App\Entity\Invoice;
use App\Entity\Settings\SystemSettings;
use App\Repository\SettingsRepositoryInterface;
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
        $defaultDueTime = $this->settingsRepository->getDefaultSettings()->getSystemSettings()->getDefaultInvoiceDueTime();

        try {
            $date = new \DateTime('+'.$defaultDueTime);
        } catch (\Throwable $e) {
            $this->getLogger()->notice('Using default due time');
            $date = new \DateTime('+'.SystemSettings::DEFAULT_DUE_TIME);
        }

        $invoice->setDueAt($date);
    }
}
