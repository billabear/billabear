<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\Customer;
use BillaBear\Entity\CustomerSubscriptionEvent;
use BillaBear\Entity\Subscription;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface CustomerSubscriptionEventRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return CustomerSubscriptionEvent[]
     */
    public function getAllForCustomer(Customer $customer): array;

    /**
     * @return CustomerSubscriptionEvent[]
     */
    public function getLastTenForCustomer(Customer $customer): array;

    /**
     * @return CustomerSubscriptionEvent[]
     */
    public function getAllForSubscription(Subscription $subscription): array;

    public function getLatest(int $limit = 10): array;
}
