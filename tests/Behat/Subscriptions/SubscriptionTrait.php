<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Subscriptions;

use Parthenon\Billing\Entity\Subscription;

trait SubscriptionTrait
{
    public function getSubscription($customerEmail, $planName): Subscription
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $subscriptionPlan = $this->planRepository->findOneBy(['name' => $planName]);

        $subscription = $this->subscriptionRepository->findOneBy(['subscriptionPlan' => $subscriptionPlan, 'customer' => $customer]);

        if (!$subscription instanceof Subscription) {
            throw new \Exception("Subscription can't be found");
        }

        $this->subscriptionRepository->getEntityManager()->refresh($subscription);

        return $subscription;
    }
}
