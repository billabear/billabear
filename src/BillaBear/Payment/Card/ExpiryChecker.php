<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\Card;

use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Subscription;

class ExpiryChecker
{
    public function hasExpiredForSubscriptionCharge(PaymentCard $paymentCard, Subscription $subscription)
    {
        $expiryDate = \DateTime::createFromFormat('Y-m-d', sprintf('%s-%s-31', $paymentCard->getExpiryYear(), $paymentCard->getExpiryMonth()));

        return $expiryDate > $subscription->getValidUntil();
    }
}
