<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\SubscriptionPlan;

use BillaBear\Entity\SubscriptionPlan;

trait SubscriptionPlanTrait
{
    protected function findSubscriptionPlanByName(string $planName): SubscriptionPlan
    {
        $subscriptionPlan = $this->subscriptionPlanRepository->findOneBy(['name' => $planName]);

        if (!$subscriptionPlan instanceof SubscriptionPlan) {
            throw new \Exception("Can't find plan");
        }

        $this->subscriptionPlanRepository->getEntityManager()->refresh($subscriptionPlan);

        return $subscriptionPlan;
    }
}
