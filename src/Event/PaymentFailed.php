<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Event;

use App\Entity\PaymentAttempt;
use App\Entity\PaymentFailureProcess;
use Parthenon\Billing\Entity\Payment;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentFailed extends Event
{
    public const NAME = 'billabear.payment.failed';

    public function __construct(private PaymentAttempt $paymentAttempt, private PaymentFailureProcess $paymentFailureProcess)
    {
    }

    public function getPaymentAttempt(): Payment
    {
        return $this->paymentAttempt;
    }

    public function getPaymentFailureProcess(): PaymentFailureProcess
    {
        return $this->paymentFailureProcess;
    }
}
