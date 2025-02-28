<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Import\Stripe;

use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Entity\StripeImport;
use BillaBear\Repository\StripeImportRepositoryInterface;
use BillaBear\Stats\SubscriptionCancellationStats;
use BillaBear\Stats\SubscriptionCreationStats;
use Obol\Model\Subscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
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
