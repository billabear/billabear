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

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Factory\PriceFactory;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Price;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class PriceImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private PriceRepositoryInterface $priceRepository,
        private PriceFactory $priceFactory,
        private StripeImportRepositoryInterface $stripeImportRepository,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true): void
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = null;
        do {
            $priceList = $provider->prices()->list($limit, $lastId);
            /** @var Price $priceModel */
            foreach ($priceList as $priceModel) {
                try {
                    $product = $this->priceRepository->getByExternalReference($priceModel->getId());
                } catch (NoEntityFoundException $exception) {
                    $product = null;
                }
                $product = $this->priceFactory->createFromObol($priceModel, $product);
                $this->priceRepository->save($product);
                $lastId = $priceModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($priceList) == $limit);
    }
}
