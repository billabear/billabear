<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
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

    public function getLatest(int $limit = 5): array
    {
        return $this->entityRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }
}
