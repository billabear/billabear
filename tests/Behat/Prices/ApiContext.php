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

        $this->sendJsonRequest('POST', '/api/v1/product/'.$product->getId().'/price', $payload);
    }

    /**
     * @Then there should be a price for :arg1 with the amount :arg2
     */
    public function thereShouldBeAPriceForWithTheAmount($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false]);

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

    /**
     * @Then there should not be a price for :arg1 with the amount :arg2
     */
    public function thereShouldNotBeAPriceForWithTheAmount($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price[] $prices */
        $prices = $this->priceRepository->findBy(['product' => $product, 'isDeleted' => false]);

        foreach ($prices as $price) {
            if ($price->getAmount() == $amount) {
                throw new \Exception('Found price');
            }
        }
    }

    /**
     * @Given the follow prices exist:
     */
    public function theFollowPricesExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $product = $this->getProductByName($row['Product']);

            $price = new Price();
            $price->setProduct($product);
            $price->setAmount($row['Amount']);
            $price->setExternalReference(bin2hex(random_bytes(12)));
            $price->setCurrency($row['Currency']);
            $price->setRecurring('true' === strtolower($row['Recurring']));
            $price->setSchedule($row['Schedule'] ?? null);
            $price->setPublic('true' === strtolower($row['Recurring'] ?? 'true'));
            $price->setCreatedAt(new \DateTime('now'));
            $this->priceRepository->getEntityManager()->persist($price);
        }
        $this->priceRepository->getEntityManager()->flush();
    }

    /**
     * @When I fetch all prices for the product :arg1 via API
     */
    public function iFetchAllPricesForTheProductViaApi($productName)
    {
        $product = $this->getProductByName($productName);
        $this->sendJsonRequest('GET', '/api/v1/product/'.$product->getId().'/price');
    }

    /**
     * @Then there should be a price for :arg1 in the data set
     */
    public function thereShouldBeAPriceForInTheDataSet($amount)
    {
        $json = $this->getJsonContent();

        foreach ($json['data'] as $price) {
            if ($amount == $price['amount']) {
                return;
            }
        }

        throw new \Exception("Can't find price");
    }
}
