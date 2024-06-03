<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Products;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Repository\Orm\ProductRepository;
use BillaBear\Repository\Orm\TaxTypeRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    public function __construct(
        private Session $session,
        private ProductRepository $productRepository,
        private TaxTypeRepository $taxTypeRepository,
    ) {
    }

    /**
     * @When I go to create a product
     */
    public function iGoToCreateAProduct()
    {
        $this->sendJsonRequest('GET', '/app/product/create');
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

        if (isset($data['Tax Type'])) {
            /** @var \BillaBear\Entity\TaxType $taxType */
            $taxType = $this->taxTypeRepository->findOneBy(['name' => $data['Tax Type']]);
            if (!$taxType instanceof \BillaBear\Entity\TaxType) {
                throw new \Exception('No tax type found');
            }

            $payload['tax_type'] = (string) $taxType->getId();
        }

        if (isset($data['Tax Rate'])) {
            $payload['tax_rate'] = floatval($data['Tax Rate']);
        }

        $this->sendJsonRequest('POST', '/app/product', $payload);
    }

    /**
     * @Then the product :arg1 should have the tax rate :arg2
     */
    public function theProductShouldHaveTheTaxRate($name, $arg2)
    {
        $product = $this->getProductByName($name);

        if ($product->getTaxRate() != $arg2) {
            throw new \Exception(sprintf('Expected %s but got %s', $arg2, $product->getTaxRate()));
        }
    }

    /**
     * @Then the product :arg1 should have the tax type :arg2
     */
    public function theProductShouldHaveTheTaxType($name, $arg2)
    {
        $product = $this->getProductByName($name);
        $taxType = $this->taxTypeRepository->findOneBy(['name' => $arg2]);
        if (!$taxType instanceof \BillaBear\Entity\TaxType) {
            throw new \Exception('No tax type found');
        }

        if ($product->getTaxType()->getId() != $taxType->getId()) {
            throw new \Exception("Tax type doesn't match");
        }
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

    /**
     * @When I update the product info via the APP for :arg1:
     */
    public function iUpdateTheProductInfoViaTheAppFor($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
        ];
        if (isset($data['Tax Type'])) {
            $taxType = $this->taxTypeRepository->findOneBy(['name' => $data['Tax Type']]);
            $payload['tax_type'] = (string) $taxType->getId();
        } else {
            $taxType = $this->taxTypeRepository->findOneBy(['default' => true]);
            $payload['tax_type'] = (string) $taxType->getId();
        }

        if (isset($data['Tax Rate'])) {
            $payload['tax_rate'] = floatval($data['Tax Rate']);
        }

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId(), $payload);
    }
}
