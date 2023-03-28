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

use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\ProductServiceRepository;

class ApiContext implements Context
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
     * @When I create a price for the product :arg1
     */
    public function iCreateAPriceForTheProduct($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();

        $payload = [
            'amount' => (int) $data['Amount'],
            'currency' => $data['Currency'],
            'recurring' => (true === strtolower($data['Recurring'])),
        ];

        $this->sendJsonRequest('POST', '/api/v1.0/product/'.$product->getId().'/price', $payload);
    }

    /**
     * @Then there should be a price for :arg1 with the amount :arg2
     */
    public function thereShouldBeAPriceForWithTheAmount($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product]);

        if (empty($prices)) {
            throw new \Exception('Price not found');
        }

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }
}
