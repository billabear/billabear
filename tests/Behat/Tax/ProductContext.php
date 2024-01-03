<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\Product;
use App\Enum\TaxType;
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

        if (TaxType::DIGITAL_GOODS !== $product->getTaxType()) {
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

        if (TaxType::DIGITAL_SERVICES !== $product->getTaxType()) {
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

        if (TaxType::PHYSICAL !== $product->getTaxType()) {
            throw new \Exception('Got a different tax type');
        }
    }
}
