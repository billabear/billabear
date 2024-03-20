<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Background\Generic;

use App\Entity\GenericBackgroundTask;
use App\Enum\GenericTask;
use App\Repository\SubscriptionRepositoryInterface;
use Obol\Model\CancelSubscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\SubscriptionFactory;

class DisableStripeBilling implements ExecutorInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private SubscriptionFactory $subscriptionFactory,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function supports(GenericBackgroundTask $backgroundTask): bool
    {
        return GenericTask::CANCEL_STRIPE_BILLING === $backgroundTask->getTask();
    }

    public function execute(GenericBackgroundTask $genericBackgroundTask): void
    {
        $subscriptions = $this->subscriptionRepository->getAll();

        foreach ($subscriptions as $subscription) {
            $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);

            $cancelRequest = new CancelSubscription();
            $cancelRequest->setSubscription($obolSubscription);
            $cancelRequest->setInstantCancel(true);

            $this->provider->payments()->stopSubscription($cancelRequest);
        }
    }
}
