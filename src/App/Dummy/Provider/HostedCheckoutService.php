<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\HostedCheckoutServiceInterface;
use Obol\Model\CheckoutCreation;
use Obol\Model\Subscription;

class HostedCheckoutService implements HostedCheckoutServiceInterface
{
    public function createCheckoutForSubscription(Subscription $subscription): CheckoutCreation
    {
        $checkoutCreation = new CheckoutCreation();
        $checkoutCreation->getCheckoutUrl('https://example.org/checkout/'.bin2hex(random_bytes(32)));

        return $checkoutCreation;
    }
}
