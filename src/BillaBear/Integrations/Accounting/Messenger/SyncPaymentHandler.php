<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use BillaBear\Repository\PaymentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncPaymentHandler
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private \BillaBear\Integrations\Accounting\Action\SyncPayment $syncPayment,
    ) {
    }

    public function __invoke(SyncPayment $message)
    {
        $payment = $this->paymentRepository->findById($message->paymentId);
        $this->syncPayment->sync($payment);
    }
}
