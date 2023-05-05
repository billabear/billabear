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
use App\Factory\SubscriptionFactory;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Model\Subscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionFactory $subscriptionFactory,
        private StripeImportRepositoryInterface $stripeImportRepository,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true): void
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = null;
        do {
            $subscriptionList = $provider->subscriptions()->list($limit, $lastId);
            /** @var Subscription $subscriptionModel */
            foreach ($subscriptionList as $subscriptionModel) {
                try {
                    $product = $this->subscriptionRepository->getForMainAndChildExternalReference($subscriptionModel->getId(), $subscriptionModel->getLineId());
                } catch (NoEntityFoundException $exception) {
                    $product = null;
                }
                $product = $this->subscriptionFactory->createFromObol($subscriptionModel, $product);
                $this->subscriptionRepository->save($product);
                $lastId = $subscriptionModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (sizeof($subscriptionList) >= $limit);
    }
}