<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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