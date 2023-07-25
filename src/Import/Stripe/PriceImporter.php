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
