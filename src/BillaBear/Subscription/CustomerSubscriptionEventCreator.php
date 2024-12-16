<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Subscription;

use BillaBear\Customer\CustomerSubscriptionEventType;
use BillaBear\Entity\CustomerSubscriptionEvent;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\CustomerSubscriptionEventRepositoryInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\CustomerInterface;

class CustomerSubscriptionEventCreator
{
    public function __construct(private CustomerSubscriptionEventRepositoryInterface $repository)
    {
    }

    public function create(
        CustomerSubscriptionEventType $eventType,
        CustomerInterface $customer,
        Subscription $subscription,
        ?BillingAdminInterface $billingAdmin = null,
    ): void {
        $event = new CustomerSubscriptionEvent();
        $event->setEventType($eventType);
        $event->setCustomer($customer);
        $event->setDoneBy($billingAdmin);
        $event->setSubscription($subscription);
        $event->setCreatedAt(new \DateTime());

        $this->repository->save($event);
    }
}
