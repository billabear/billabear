<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event\Customer;

use BillaBear\Entity\Customer;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerCreated extends Event
{
    public const string  NAME = 'billabear.customer.created';

    public function __construct(public readonly Customer $customer)
    {
    }
}
