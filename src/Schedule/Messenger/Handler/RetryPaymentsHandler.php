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

namespace App\Schedule\Messenger\Handler;

use App\Background\Payments\RetryPaymentsProcess;
use App\Schedule\Messenger\Message\RetryPayments;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RetryPaymentsHandler
{
    public function __construct(private RetryPaymentsProcess $paymentsProcess)
    {
    }

    public function __invoke(RetryPayments $payments)
    {
        $this->paymentsProcess->execute();
    }
}
