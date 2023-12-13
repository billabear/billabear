<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
