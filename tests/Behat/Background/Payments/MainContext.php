<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Background\Payments;

use Behat\Behat\Context\Context;
use BillaBear\Background\Payments\RetryPaymentsProcess;

class MainContext implements Context
{
    public function __construct(
        private RetryPaymentsProcess $paymentsProcess
    ) {
    }

    /**
     * @When I retry failed payments
     */
    public function iRetryFailedPayments()
    {
        $this->paymentsProcess->execute();
    }
}
