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

namespace App\Tests\Behat\Products;

use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\Orm\ProductServiceRepository;

class ApiContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private ProductServiceRepository $productRepository)
    {
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

        $this->sendJsonRequest('POST', '/api/v1.0/product', $payload);
    }

    /**
     * @Then there should be a product with the name :arg1
     */
    public function thereShouldBeAProductWithTheName($name)
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if (!$product) {
            throw new \Exception("Can't find product");
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
            $this->productRepository->getEntityManager()->persist($product);
        }
        $this->productRepository->getEntityManager()->flush();
    }

    /**
     * @When I use the API to list product
     */
    public function iUseTheApiToListProduct()
    {
        $this->sendJsonRequest('GET', '/api/v1.0/product');
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
        $this->sendJsonRequest('GET', sprintf('/api/v1.0/product?%s=%s', $filter, $value));
    }
}
