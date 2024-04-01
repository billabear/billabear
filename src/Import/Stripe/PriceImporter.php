<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Import\Stripe;

use App\DataMappers\PriceDataMapper;
use App\Entity\StripeImport;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Price;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class PriceImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private PriceRepositoryInterface $priceRepository,
        private PriceDataMapper $priceFactory,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true): void
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $priceList = $provider->prices()->list($limit, $lastId);
            /** @var Price $priceModel */
            foreach ($priceList as $priceModel) {
                try {
                    $price = $this->priceRepository->getByExternalReference($priceModel->getId());
                } catch (NoEntityFoundException $exception) {
                    $price = null;
                }
                $price = $this->priceFactory->createFromObol($priceModel, $price);
                $this->priceRepository->save($price);

                $plans = $this->subscriptionPlanRepository->getAllForProduct($price->getProduct());
                foreach ($plans as $plan) {
                    $plan->addPrice($price);
                    $this->subscriptionPlanRepository->save($plan);
                }

                $lastId = $priceModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($priceList) == $limit);
        $stripeImport->setLastId(null);
    }
}
