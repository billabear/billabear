<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event;

use BillaBear\Entity\PaymentAttempt;
use BillaBear\Entity\PaymentFailureProcess;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentFailed extends Event
{
    public const NAME = 'billabear.payment.failed';

    public function __construct(private PaymentAttempt $paymentAttempt, private PaymentFailureProcess $paymentFailureProcess)
    {
    }

    public function getPaymentAttempt(): PaymentAttempt
    {
        return $this->paymentAttempt;
    }

    public function getPaymentFailureProcess(): PaymentFailureProcess
    {
        return $this->paymentFailureProcess;
    }
}
