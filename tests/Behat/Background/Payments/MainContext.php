<?php

/*
 * Copyright all rights reserved. No public license given.
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
