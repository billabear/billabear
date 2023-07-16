<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\DataMappers\ProductFactory;
use App\Entity\StripeImport;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Product;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class ProductImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private ProductRepositoryInterface $productRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private ProductFactory $productFactory,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $productList = $provider->products()->list($limit, $lastId);
            /** @var Product $productModel */
            foreach ($productList as $productModel) {
                try {
                    $product = $this->productRepository->getByExternalReference($productModel->getId());
                } catch (NoEntityFoundException $exception) {
                    $product = null;
                }
                $product = $this->productFactory->createFromObol($productModel, $product);

                $this->productRepository->save($product);

                $subscriptionPlan = new SubscriptionPlan();
                $subscriptionPlan->setName($product->getName());
                $subscriptionPlan->setProduct($product);
                $subscriptionPlan->setPerSeat(false);
                $subscriptionPlan->setFree(false);
                $subscriptionPlan->setHasTrial(false);
                $subscriptionPlan->setUserCount(1);
                $this->subscriptionPlanRepository->save($subscriptionPlan);
                $lastId = $productModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($productList) == $limit);
        $stripeImport->setLastId(null);
    }
}
