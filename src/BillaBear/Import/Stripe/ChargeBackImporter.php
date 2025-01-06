<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\DataMappers\ChargeBackDataMapper;
use BillaBear\Entity\StripeImport;
use BillaBear\Repository\StripeImportRepositoryInterface;
use BillaBear\Stats\ChargeBackAmountStats;
use Obol\Model\ChargeBack\ChargeBack;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\ChargeBackRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
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
