<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Prices;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\Price;
use BillaBear\Repository\Orm\MetricRepository;
use BillaBear\Repository\Orm\PriceRepository;
use BillaBear\Repository\Orm\ProductRepository;
use BillaBear\Tests\Behat\Products\ProductTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    private array $tiers = [];

    public function __construct(
        private Session $session,
        private ProductRepository $productRepository,
        private PriceRepository $priceRepository,
        private MetricRepository $metricRepository,
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $this->tiers = [];
    }

    /**
     * @Given I configure tiers pricing of:
     */
    public function iConfigureTiersPricingOf(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $this->tiers[] = [
                'first_unit' => (int) $row['First Unit'],
                'last_unit' => (int) $row['Last Unit'],
                'unit_price' => (int) $row['Unit Price'],
                'flat_fee' => (int) $row['Flat Fee'],
            ];
        }
    }

    /**
     * @When I create a price via the app for the product :arg1
     */
    public function iCreateAPriceViaTheAppForTheProduct($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();

        $payload = [
            'currency' => $data['Currency'],
            'recurring' => ('true' === strtolower($data['Recurring'] ?? 'false')),
        ];

        if (isset($data['Amount'])) {
            $payload['amount'] = (int) $data['Amount'];
        }

        if (isset($data['Type'])) {
            $payload['type'] = str_replace(' ', '_', strtolower($data['Type']));
        }

        if (isset($data['Units'])) {
            $payload['units'] = intval($data['Units']);
        }

        if (isset($data['Usage'])) {
            $payload['usage'] = ('true' === strtolower($data['Usage']));
        }

        if (isset($data['Metric'])) {
            $metric = $this->metricRepository->findOneBy(['name' => $data['Metric']]);
            $payload['metric'] = (string) $metric->getId();
        }

        if (isset($data['Metric Type'])) {
            $payload['metric_type'] = strtolower($data['Metric Type']);
        }

        if (!empty($this->tiers)) {
            $payload['tiers'] = $this->tiers;
        }

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
