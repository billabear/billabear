<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\Product;
use App\Repository\Orm\ProductRepository;
use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;

class ProductContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;

    public function __construct(
        private Session $session,
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @Then the tax type for product :arg1 is Digital Goods
     */
    public function theTaxTypeForProductIsDigitalGoods($productName)
    {
        /** @var Product $product */
        $product = $this->getProductByName($productName);

        if ('Digital Goods' !== $product->getTaxType()->getName()) {
            throw new \Exception('Got a different tax type');
        }
    }

    /**
     * @Then the tax type for product :arg1 is Digital Service
     */
    public function theTaxTypeForProductIsDigitalService($productName)
    {
        /** @var Product $product */
        $product = $this->getProductByName($productName);

        if ('Digital Services' !== $product->getTaxType()->getName()) {
            throw new \Exception('Got a different tax type');
        }
    }

    /**
     * @Then the tax type for product :arg1 is Physical
     */
    public function theTaxTypeForProductIsPhysical($productName)
    {
        /** @var Product $product */
        $product = $this->getProductByName($productName);

        if ('Physical' !== $product->getTaxType()->getName()) {
            throw new \Exception('Got a different tax type');
        }
    }
}
