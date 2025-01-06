<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Repository\PaymentFailureProcessRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentFailureProcessor
{
    public function __construct(
        private PaymentFailureProcessRepositoryInterface $paymentFailureProcessRepository,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function processPaymentFailure(Customer $customer, ?Invoice $invoice = null): void
    {
    }
}
