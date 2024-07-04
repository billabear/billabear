<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Background\Subscription;

use BillaBear\Repository\MassSubscriptionChangeRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\SubscriptionUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;

class MassChange
{
    public function __construct(
        private MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionManagerInterface $subscriptionManager,
        private WebhookDispatcherInterface $webhookDispatcher,
    ) {
    }

    public function execute(): void
    {
        $massChanges = $this->massSubscriptionChangeRepository->findWithinFiveMinutes(new \DateTime('now'));

        foreach ($massChanges as $massChange) {
            $subscriptions = $this->subscriptionRepository->findMassChangable(
                $massChange->getTargetSubscriptionPlan(),
                $massChange->getTargetPrice(),
                $massChange->getBrandSettings(),
                $massChange->getTargetCountry(),
            );

            foreach ($subscriptions as $subscription) {
                if ($massChange->getNewPrice()) {
                    $this->subscriptionManager->changeSubscriptionPrice($subscription, $massChange->getNewPrice(), BillingChangeTiming::NEXT_CYCLE);
                }

                if ($massChange->getNewSubscriptionPlan()) {
                    $subscription->setSubscriptionPlan($massChange->getNewSubscriptionPlan());
                }

                $this->webhookDispatcher->dispatch(new SubscriptionUpdatedPayload($subscription));
                $this->subscriptionRepository->save($subscription);
            }
        }
    }
}
