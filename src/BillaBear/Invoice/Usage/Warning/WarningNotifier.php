<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Usage\Warning;

use BillaBear\Entity\Customer;
use BillaBear\Entity\UsageLimit;
use Brick\Money\Money;

class WarningNotifier
{
    public function __construct()
    {
    }

    public function notify(Customer $customer, UsageLimit $usageLimit, Money $amount): void
    {
    }
}
