<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Workflows;

use BillaBear\Dto\Generic\App\Workflows\PaymentFailureProcess;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewPaymentFailureProcess
{
    #[SerializedName('payment_failure_process')]
    private PaymentFailureProcess $paymentFailureProcess;

    public function getPaymentFailureProcess(): PaymentFailureProcess
    {
        return $this->paymentFailureProcess;
    }

    public function setPaymentFailureProcess(PaymentFailureProcess $paymentFailureProcess): void
    {
        $this->paymentFailureProcess = $paymentFailureProcess;
    }
}
