<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Import\Stripe;

use App\Entity\StripeImport;
use App\Factory\RefundFactory;
use App\Repository\StripeImportRepositoryInterface;
use App\Stats\RefundAmountStats;
use Obol\Model\Refund;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class RefundImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private RefundRepositoryInterface $refundRepository,
        private RefundFactory $factory,
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
