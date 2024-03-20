<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\Portal\Checkout;

use App\Dto\Response\Portal\Quote\StripeInfo;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerCreation
{
    protected StripeInfo $stripe;

    #[SerializedName('checkout_session')]
    private CheckoutSession $checkoutSession;

    public function getCheckoutSession(): CheckoutSession
    {
        return $this->checkoutSession;
    }

    public function setCheckoutSession(CheckoutSession $checkoutSession): void
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function getStripe(): StripeInfo
    {
        return $this->stripe;
    }

    public function setStripe(StripeInfo $stripe): void
    {
        $this->stripe = $stripe;
    }
}
