<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Prices;

use App\Entity\Price;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\ProductRepository;
use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class ApiContext implements Context
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
            if (isset($row['Schedule']) && !empty($row['Schedule'])) {
                $price->setSchedule($row['Schedule']);
            }
            $price->setPublic('true' === strtolower($row['Public'] ?? 'true'));
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
