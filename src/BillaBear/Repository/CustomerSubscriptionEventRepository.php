<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Entity\SubscriptionPlan;
use Parthenon\Athena\Repository\DoctrineCrudRepository;

class CustomerSubscriptionEventRepository extends DoctrineCrudRepository implements CustomerSubscriptionEventRepositoryInterface
{
    public function getAllForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC']);
    }

    public function getLastTenForCustomer(Customer $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC'], 10);
    }

    public function getAllForSubscription(Subscription $subscription): array
    {
        return $this->entityRepository->findBy(['subscription' => $subscription]);
    }

    public function getLatest(int $limit = 10): array
    {
        return $this->entityRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function hasTrialStartedEventForCustomerAndPlan(Customer $customer, SubscriptionPlan $subscriptionPlan): bool
    {
        $events = $this->entityRepository->findBy([
            'customer' => $customer,
            'eventType' => CustomerSubscriptionEventType::TRIAL_STARTED,
        ]);

        foreach ($events as $event) {
            $subscription = $event->getSubscription();
            if ($subscription && $subscription->getSubscriptionPlan()->getId() === $subscriptionPlan->getId()) {
                return true;
            }
        }

        return false;
    }
}
