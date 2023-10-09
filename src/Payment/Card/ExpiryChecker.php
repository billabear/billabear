<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment\Card;

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
