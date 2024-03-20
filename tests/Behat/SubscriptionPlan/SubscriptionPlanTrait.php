<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\SubscriptionPlan;

use App\Entity\SubscriptionPlan;

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
