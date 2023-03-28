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

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    public function __construct(private Session $session, private ProductServiceRepository $productRepository)
    {
    }

    /**
     * @When I create a product via the app with the following info
     */
    public function iCreateAProductViaTheAppWithTheFollowingInfo(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
        ];

        $this->sendJsonRequest('POST', '/app/product', $payload);
    }

    /**
     * @When I use the APP to list product
     */
    public function iUseTheAppToListProduct()
    {
        $this->sendJsonRequest('GET', '/app/product');
    }

    /**
     * @When I use the APP to view product :arg1
     */
    public function iUseTheAppToViewProduct($name)
    {
        $product = $this->getProductByName($name);

        $this->sendJsonRequest('GET', '/app/product/'.$product->getId());
    }
}
