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

namespace App\Tests\Behat\Products;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Repository\Orm\ProductServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private ProductServiceRepository $productRepository)
    {
    }

    /**
     * @When I create a product with the following info
     */
    public function iCreateAProductWithTheFollowingInfo(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
        ];

        $this->sendJsonRequest('POST', '/api/v1.0/product', $payload);
    }

    /**
     * @Then there should be a product with the name :arg1
     */
    public function thereShouldBeAProductWithTheName($name)
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if (!$product) {
            throw new \Exception("Can't find product");
        }
    }
}
