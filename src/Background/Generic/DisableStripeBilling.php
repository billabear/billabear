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

            $cancellation = $this->provider->payments()->stopSubscription($cancelRequest);
        }
    }
}
