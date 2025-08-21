<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Generic;

use BillaBear\Entity\GenericBackgroundTask;
use BillaBear\Enum\GenericTask;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Obol\Model\CancelSubscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Obol\SubscriptionFactory;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

#[Lazy]
readonly class DisableStripeBilling implements ExecutorInterface
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
