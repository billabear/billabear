<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Prices;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\Price;
use BillaBear\Repository\Orm\PriceRepository;
use BillaBear\Repository\Orm\ProductRepository;
use BillaBear\Tests\Behat\Products\ProductTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

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

    /**
     * @When I use the APP to make a price from :arg1 for :arg3 per :arg2 private
     */
    public function iUseTheAppToMakeAPriceFromForPerPrivate($productName, $amount, $schedule)
    {
        $product = $this->getProductByName($productName);

        $price = $this->priceRepository->findOneBy(['product' => $product, 'amount' => $amount, 'schedule' => $schedule]);

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/price/'.$price->getId().'/private');
    }

    /**
     * @Then the price for :arg1 with the amount :arg2 should be private
     */
    public function thePriceForWithTheAmountShouldBePrivate($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $product, 'amount' => $amount]);
        $this->priceRepository->getEntityManager()->refresh($price);

        if ($price->isPublic()) {
            var_dump($this->getJsonContent());
            throw new \Exception('Price is not private');
        }
    }

    /**
     * @When I use the APP to make a price from :arg1 for :arg3 per :arg2 public
     */
    public function iUseTheAppToMakeAPriceFromForPerPublic($productName, $amount, $schedule)
    {
        $product = $this->getProductByName($productName);

        $price = $this->priceRepository->findOneBy(['product' => $product, 'amount' => $amount, 'schedule' => $schedule]);

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/price/'.$price->getId().'/public');
    }

    /**
     * @Then the price for :arg1 with the amount :arg2 should be public
     */
    public function thePriceForWithTheAmountShouldBePublic($productName, $amount)
    {
        $product = $this->getProductByName($productName);

        /** @var Price $price */
        $price = $this->priceRepository->findOneBy(['product' => $product, 'amount' => $amount]);
        $this->priceRepository->getEntityManager()->refresh($price);

        if (!$price->isPublic()) {
            throw new \Exception('Price is private');
        }
    }
}
