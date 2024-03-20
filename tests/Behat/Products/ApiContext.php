<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Products;

use App\Entity\Product;
use App\Repository\Orm\ProductRepository;
use App\Repository\Orm\TaxTypeRepository;
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
        private TaxTypeRepository $taxTypeRepository,
    ) {
    }

    /**
     * @When I create a product with the following info
     */
    public function iCreateAProductWithTheFollowingInfo(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
        ];

        $this->sendJsonRequest('POST', '/api/v1/product', $payload);
    }

    /**
     * @Then there should be a product with the name :arg1
     */
    public function thereShouldBeAProductWithTheName($name)
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if (!$product) {
            var_dump($this->getJsonContent());
            throw new \Exception("Can't find product");
        }
    }

    /**
     * @Then there should not be a product with the name :arg1
     */
    public function thereShouldNotBeAProductWithTheName($name)
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if ($product) {
            throw new \Exception('Found product');
        }
    }

    /**
     * @Given the follow products exist:
     */
    public function theFollowProductsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $product = new Product();
            $product->setName($row['Name']);
            $product->setExternalReference($row['External Reference'] ?? null);

            $taxTypeName = $row['Tax Type'] ?? 'default';
            $taxType = $this->taxTypeRepository->findOneBy(['name' => $taxTypeName]);
            $product->setTaxType($taxType);

            if (isset($row['Tax Rate']) && !empty($row['Tax Rate'])) {
                $product->setTaxRate(floatval($row['Tax Rate']));
            }
            $this->productRepository->getEntityManager()->persist($product);
        }
        $this->productRepository->getEntityManager()->flush();
    }

    /**
     * @When I use the API to list product
     */
    public function iUseTheApiToListProduct()
    {
        $this->sendJsonRequest('GET', '/api/v1/product');
    }

    /**
     * @Then I should see in the API response the product :arg1
     */
    public function iShouldSeeInTheApiResponseTheProduct($name)
    {
        $data = $this->getJsonContent();

        if (!isset($data['data'])) {
            throw new \Exception('No data found');
        }

        foreach ($data['data'] as $product) {
            if ($product['name'] === $name) {
                return;
            }
        }

        throw new \Exception("Can't find product");
    }

    /**
     * @When I use the API to list products with parameter :arg1 with value :arg2
     */
    public function iUseTheApiToListProductsWithParameterWithValue($filter, $value)
    {
        $this->sendJsonRequest('GET', sprintf('/api/v1/product?%s=%s', $filter, $value));
    }

    /**
     * @When I use the API to view product :arg1
     */
    public function iUseTheApiToViewProduct($name)
    {
        $product = $this->getProductByName($name);

        $this->sendJsonRequest('GET', '/api/v1/product/'.$product->getId());
    }

    /**
     * @When I update the product info via the API for :arg1:
     */
    public function iUpdateTheProductInfoViaTheApiFor($name, TableNode $table)
    {
        $product = $this->getProductByName($name);

        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
        ];

        $this->sendJsonRequest('PUT', '/api/v1/product/'.$product->getId(), $payload);
    }
}
