<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Workflows;

use App\Dto\Generic\App\Workflows\PaymentCreation;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewPaymentCreation
{
    #[SerializedName('payment_creation')]
    private PaymentCreation $paymentCreation;

    public function getPaymentCreation(): PaymentCreation
    {
        return $this->paymentCreation;
    }

    public function setPaymentCreation(PaymentCreation $paymentCreation): void
    {
        $this->paymentCreation = $paymentCreation;
    }
}
