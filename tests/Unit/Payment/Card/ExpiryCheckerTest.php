<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Unit\Payment\Card;

use App\Payment\Card\ExpiryChecker;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class ExpiryCheckerTest extends TestCase
{
    public function testCardWillBeValid()
    {
        $paymentCard = new PaymentCard();
        $paymentCard->setExpiryYear(2023);
        $paymentCard->setExpiryMonth(10);

        $subscription = new Subscription();
        $subscription->setValidUntil(new \DateTime('2023-05-05'));

        $subject = new ExpiryChecker();

        $this->assertTrue($subject->hasExpiredForSubscriptionCharge($paymentCard, $subscription));
    }

    public function testCardWillNotBeValid()
    {
        $paymentCard = new PaymentCard();
        $paymentCard->setExpiryYear(2023);
        $paymentCard->setExpiryMonth(10);

        $subscription = new Subscription();
        $subscription->setValidUntil(new \DateTime('2023-11-05'));

        $subject = new ExpiryChecker();

        $this->assertFalse($subject->hasExpiredForSubscriptionCharge($paymentCard, $subscription));
    }
}
