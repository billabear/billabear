<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\DataMappers\ChargeBackDataMapper;
use App\Entity\StripeImport;
use App\Repository\StripeImportRepositoryInterface;
use App\Stats\ChargeBackAmountStats;
use Obol\Model\ChargeBack\ChargeBack;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\ChargeBackRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class ChargeBackImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private ChargeBackRepositoryInterface $chargeBackRepository,
        private ChargeBackDataMapper $factory,
        private ChargeBackAmountStats $amountStats,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $paymentList = $provider->chargeBacks()->list($limit, $lastId);
            /** @var ChargeBack $obolChargeBack */
            foreach ($paymentList as $obolChargeBack) {
                try {
                    $chargeBack = $this->chargeBackRepository->getByExternalReference($obolChargeBack->getId());
                } catch (NoEntityFoundException $e) {
                    $chargeBack = null;
                }
                $chargeBack = $this->factory->createEntity($obolChargeBack, $chargeBack);

                $this->chargeBackRepository->save($chargeBack);
                $this->amountStats->process($chargeBack);
                $lastId = $obolChargeBack->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($paymentList) == $limit);
        $stripeImport->setLastId(null);
    }
}
