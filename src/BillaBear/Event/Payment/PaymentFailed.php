<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Event\Payment;

use BillaBear\Entity\PaymentAttempt;
use BillaBear\Entity\PaymentFailureProcess;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentFailed extends Event
{
    public const string NAME = 'billabear.payment.failed';

    public function __construct(
        public readonly PaymentAttempt $paymentAttempt,
        public readonly PaymentFailureProcess $paymentFailureProcess,
    ) {
    }
}
