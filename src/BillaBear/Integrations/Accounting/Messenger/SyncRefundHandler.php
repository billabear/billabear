<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting\Messenger;

use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncRefundHandler
{
    public function __construct(
        private \BillaBear\Integrations\Accounting\Action\SyncRefund $syncRefund,
        private readonly RefundRepositoryInterface $refundRepository,
    ) {
    }

    public function __invoke(SyncRefund $message)
    {
        $payment = $this->refundRepository->findById($message->refundId);
        $this->syncRefund->sync($payment);
    }
}
