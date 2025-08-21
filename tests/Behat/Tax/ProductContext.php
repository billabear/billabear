<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use BillaBear\Entity\Product;
use BillaBear\Repository\Orm\ProductRepository;
use BillaBear\Tests\Behat\Products\ProductTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

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
