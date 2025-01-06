<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\Entity\StripeImport;
use BillaBear\Repository\StripeImportRepositoryInterface;
use BillaBear\Stats\RefundAmountStats;
use Obol\Model\Refund;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class RefundImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private RefundRepositoryInterface $refundRepository,
        private RefundDataMapper $factory,
        private RefundAmountStats $amountStats,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true)
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $paymentList = $provider->refunds()->list($limit, $lastId);
            /** @var Refund $obolRefund */
            foreach ($paymentList as $obolRefund) {
                try {
                    $refund = $this->refundRepository->getForExternalReference($obolRefund->getId());
                } catch (NoEntityFoundException $e) {
                    $refund = null;
                }
                $refund = $this->factory->createEntity($obolRefund, $refund);

                $this->refundRepository->save($refund);
                $this->amountStats->process($refund);
                $lastId = $obolRefund->getId();
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
