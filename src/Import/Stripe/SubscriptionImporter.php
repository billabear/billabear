<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Import\Stripe;

use App\DataMappers\Subscriptions\SubscriptionDataMapper;
use App\Entity\StripeImport;
use App\Repository\StripeImportRepositoryInterface;
use App\Stats\SubscriptionCancellationStats;
use App\Stats\SubscriptionCreationStats;
use Obol\Model\Subscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionImporter
{
    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionDataMapper $subscriptionFactory,
        private StripeImportRepositoryInterface $stripeImportRepository,
        private SubscriptionCreationStats $subscriptionCreationStats,
        private SubscriptionCancellationStats $subscriptionCancellationStats,
    ) {
    }

    public function import(StripeImport $stripeImport, bool $save = true): void
    {
        $provider = $this->provider;
        $limit = 25;
        $lastId = $save ? $stripeImport->getLastId() : null;
        do {
            $subscriptionList = $provider->subscriptions()->list($limit, $lastId);

            /** @var Subscription $subscriptionModel */
            foreach ($subscriptionList as $subscriptionModel) {
                try {
                    $subscription = $this->subscriptionRepository->getForMainAndChildExternalReference($subscriptionModel->getId(), $subscriptionModel->getLineId());
                } catch (NoEntityFoundException $exception) {
                    $subscription = null;
                }
                $subscription = $this->subscriptionFactory->createFromObol($subscriptionModel, $subscription);
                $this->subscriptionRepository->save($subscription);
                $this->subscriptionCreationStats->handleStats($subscription);

                if ($subscription->getEndedAt()) {
                    $this->subscriptionCancellationStats->handleStats($subscription);
                }
                $lastId = $subscriptionModel->getId();
            }
            $stripeImport->setLastId($lastId);
            $stripeImport->setUpdatedAt(new \DateTime());
            if ($save) {
                $this->stripeImportRepository->save($stripeImport);
            }
        } while (!empty($subscriptionList));
        $stripeImport->setLastId(null);
    }
}
