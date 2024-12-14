<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event\Customer;

use BillaBear\Entity\Customer;

class CustomerEnabled
{
    public const NAME = 'billabear.customer.enabled';

    public function __construct(public readonly Customer $customer)
    {
    }
}
