<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Payment;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Repository\PaymentFailureProcessRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentFailureProcessor
{
    public function __construct(
        private PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function processPaymentFailure(Customer $customer, Invoice $invoice = null): void
    {
    }
}
