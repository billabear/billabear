<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
