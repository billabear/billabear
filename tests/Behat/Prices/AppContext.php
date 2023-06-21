<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Prices;

use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\ProductServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    public function __construct(
        private Session $session,
        private ProductServiceRepository $productRepository,
        private PriceServiceRepository $priceRepository,
    ) {
    }

    /**
     * @When I create a price via the app for the product :arg1
     */
    public function iCreateAPriceViaTheAppForTheProduct($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();

        $payload = [
            'amount' => (int) $data['Amount'],
            'currency' => $data['Currency'],
            'recurring' => ('true' === strtolower($data['Recurring'])),
        ];

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/price', $payload);
    }

    /**
     * @When I use the APP to delete a price from :arg1 for :arg3 per :arg2
     */
    public function iUseTheAppToDeleteAPriceFromForPer($productName, $amount, $schedule)
    {
        $product = $this->getProductByName($productName);

        $price = $this->priceRepository->findOneBy(['product' => $product, 'amount' => $amount, 'schedule' => $schedule]);

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/price/'.$price->getId().'/delete');
    }
}
