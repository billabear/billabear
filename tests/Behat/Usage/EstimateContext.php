<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Usage;

use Behat\Behat\Context\Context;

class EstimateContext implements Context
{
    /**
     * @When then the estimate for the customer :arg1 will be for :arg3 :arg2
     */
    public function thenTheEstimateForTheCustomerWillBeFor($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }
}
