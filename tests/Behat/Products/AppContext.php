<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Products;

use App\Repository\Orm\ProductRepository;
use App\Repository\Orm\TaxTypeRepository;
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
            /** @var \App\Entity\TaxType $taxType */
            $taxType = $this->taxTypeRepository->findOneBy(['name' => $data['Tax Type']]);
            if (!$taxType instanceof \App\Entity\TaxType) {
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
