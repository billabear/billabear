<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Payment\Card;

use BillaBear\Payment\Card\ExpiryChecker;
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
