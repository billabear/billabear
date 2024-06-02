<?php

/*
 * Copyright all rights reserved. No public license given.
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
