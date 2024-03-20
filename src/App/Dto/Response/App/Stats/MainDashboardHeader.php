<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Stats;

use Symfony\Component\Serializer\Annotation\SerializedName;

class MainDashboardHeader
{
    #[SerializedName('active_subscriptions')]
    private int $activeSubscriptions;

    #[SerializedName('active_customers')]
    private int $activeCustomers;

    #[SerializedName('unpaid_invoices_count')]
    private int $unpaidInvoicesCount;

    #[SerializedName('unpaid_invoices_amount')]
    private int $unpaidInvoicesAmount;

    public function getActiveSubscriptions(): int
    {
        return $this->activeSubscriptions;
    }

    public function setActiveSubscriptions(int $activeSubscriptions): void
    {
        $this->activeSubscriptions = $activeSubscriptions;
    }

    public function getUnpaidInvoicesCount(): int
    {
        return $this->unpaidInvoicesCount;
    }

    public function setUnpaidInvoicesCount(int $unpaidInvoicesCount): void
    {
        $this->unpaidInvoicesCount = $unpaidInvoicesCount;
    }

    public function getUnpaidInvoicesAmount(): int
    {
        return $this->unpaidInvoicesAmount;
    }

    public function setUnpaidInvoicesAmount(int $unpaidInvoicesAmount): void
    {
        $this->unpaidInvoicesAmount = $unpaidInvoicesAmount;
    }

    public function getActiveCustomers(): int
    {
        return $this->activeCustomers;
    }

    public function setActiveCustomers(int $activeCustomers): void
    {
        $this->activeCustomers = $activeCustomers;
    }
}
