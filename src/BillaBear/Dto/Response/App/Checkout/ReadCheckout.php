<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Checkout;

use BillaBear\Dto\Generic\App\Checkout;

class ReadCheckout
{
    private Checkout $checkout;

    public function getCheckout(): Checkout
    {
        return $this->checkout;
    }

    public function setCheckout(Checkout $checkout): void
    {
        $this->checkout = $checkout;
    }
}
