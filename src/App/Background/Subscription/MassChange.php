<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Background\Subscription;

use App\Repository\MassSubscriptionChangeRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;

class MassChange
{
    public function __construct(
        private MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionManagerInterface $subscriptionManager,
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

                $this->subscriptionRepository->save($subscription);
            }
        }
    }
}
