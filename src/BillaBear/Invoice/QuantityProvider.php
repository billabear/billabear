<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

use BillaBear\Entity\Subscription;
use BillaBear\Repository\SettingsRepositoryInterface;

class QuantityProvider
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function getQuantity(float|int $quantity, \DateTime $when, Subscription $subscription): float|int
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (InvoiceGenerationType::PERIODICALLY === $settings->getSystemSettings()->getInvoiceGenerationType()) {
            return $quantity;
        }

        $daysInMonth = $when->format('t');
        $daysLeft = abs($subscription->getValidUntil()->diff($when)->days);
        $daysToBill = $daysInMonth / $daysLeft;

        return round($quantity / $daysToBill, 2);
    }
}
