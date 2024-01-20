<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\TaxType;
use App\Repository\Orm\TaxTypeRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class TaxTypeContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private TaxTypeRepository $taxTypeRepository,
    ) {
    }

    /**
     * @When I create a tax type with the name :arg1
     */
    public function iCreateATaxTypeWithTheName($name)
    {
        $this->sendJsonRequest('POST', '/app/tax/type', ['name' => $name]);
    }

    /**
     * @Then there will be a tax type with the name :arg1
     */
    public function thereWillBeATaxTypeWithTheName($name)
    {
        $taxType = $this->taxTypeRepository->findOneBy(['name' => $name]);

        if (!$taxType instanceof TaxType) {
            throw new \Exception('Tax type not found');
        }
    }
}
