<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
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
