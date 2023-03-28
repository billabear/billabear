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

namespace App\Tests\Behat\Prices;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

class AppContext implements Context
{
    /**
     * @When I create a price for the product :arg1 via APP
     */
    public function iCreateAPriceForTheProduct($name, TableNode $table)
    {
        throw new PendingException();
    }
}
