<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Public\Checkout;

use Symfony\Component\Serializer\Annotation\SerializedName;

class PayCheckout
{
    #[SerializedName('checkout_session')]
    private $checkoutSession;

    private $token;

    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    public function setCheckoutSession($checkoutSession): void
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }
}
