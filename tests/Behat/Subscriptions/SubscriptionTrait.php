<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\Subscriptions;

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
