<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event\Checkout;

use BillaBear\Entity\Checkout;
use Symfony\Contracts\EventDispatcher\Event;

class CheckoutCreated extends Event
{
    public const string NAME = 'billabear.checkout.created';

    public function __construct(public readonly Checkout $checkout)
    {
    }
}
