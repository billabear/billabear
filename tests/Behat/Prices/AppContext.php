<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Prices;

use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\ProductRepository;
use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    public function __construct(
        private Session $session,
        private ProductRepository $productRepository,
        private PriceRepository $priceRepository,
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
